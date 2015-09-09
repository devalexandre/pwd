<?php
Namespace Pwd\PTime;

use  Pwd\PTime\SuperClosure;


	// Hide notices and errors from curl
	error_reporting(0);
	ini_set('display_errors', 0);

	Class Thread {
		private $password = '785tghjguigu'; //change this
		private $salt = 'DfEQn8*#^2n!9jErF'; //and this
		private $max_threads = 5; //max synchronous requests, try more or less

		public function __construct(){
			if(!empty($_SERVER['HTTP_PHPTHREADS'])){
					$closure = $_POST['PHPThreads_Run'];
					$closure = $this->strcode(base64_decode($closure), $this->password);

					$unserialized_closure = unserialize($closure);
					if(gettype($unserialized_closure) != 'object') return false;

					ob_start();
					$response = $unserialized_closure();
					$print = ob_get_contents();
					ob_end_clean();

					echo serialize(array(
						'return' => $response,
						'print' => $print
					));
					die();
			}

			$this->output = array();
		}

		public function Create($func, $variables = false){
			if(gettype($func) != 'object'){
				trigger_error("Thread must be a function", E_USER_NOTICE);
				return false;
			}
			$thread =  new SuperClosure($func);
			$serialized_closure = serialize($thread);
			$serialized_variables = serialize($variables);
			$this->threads[] = array(
				$serialized_closure,
				$serialized_variables
			);
		}

		public function Clear(){
			unset($this->threads);
			$this->output = array();
		}

		public function Run($echo = true){
				if(!is_array($this->threads)) return false;

				//Start
				$tasks = array();

				foreach ($this->threads as $i=>$thread) {
					$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch,CURLOPT_HTTPHEADER,
						array('PHPThreads: true')
					);
					curl_setopt($ch, CURLOPT_POST, 1);

					$Post = array(
						'PHPThreads_Run' => base64_encode($this->strcode($thread[0], $this->password))
					);

					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Post));

					$tasks[$i] = $ch;
				}

				$this->rolling_curl($tasks);
				foreach($this->output as $i=>$response){
					$response = unserialize($response);
					if($echo) echo $response['print'];
					$resp[$i] = $response['return'];
				}
				$this->Clear(); //Clear Threads after run

				if(is_array($resp)) ksort($resp);
				return $resp;
				// End
		}

		private function callback($output, $error = false){
			$this->output[] = $output;
		}
		private function rolling_curl($multi_handles) {
	    $rolling_window = $this->max_threads;
	    $rolling_window = (sizeof($multi_handles) < $rolling_window) ? sizeof($multi_handles) : $rolling_window;

	    $master = curl_multi_init();
	    $curl_arr = array();


	    // start the first batch of requests
	    for ($i = 0; $i < $rolling_window; $i++) {
	        curl_multi_add_handle($master, $multi_handles[$i]);
	    }

	    do {
	        while(($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM);
	        if($execrun != CURLM_OK)
	            break;
	        // a request was just completed -- find out which one
	        while($done = curl_multi_info_read($master)) {
	            $info = curl_getinfo($done['handle']);
	            if ($info['http_code'] == 200)  {
	                $output = curl_multi_getcontent($done['handle']);

	                // request successful.  process output using the callback function.
	                $this->callback($output);

	                curl_multi_add_handle($master, $multi_handles[$i++]);

	                // remove the curl handle that just completed
	                curl_multi_remove_handle($master, $done['handle']);
									curl_multi_select($master);
	            } else {
	                // request failed.  add error handling.
									$this->callback($info, true);
	            }
	        }
	    } while ($running);

	    curl_multi_close($master);
	    return true;
		}


		private function strcode($str, $passw=""){
			$salt = $this->salt;
			$len = strlen($str);
			$gamma = '';
			$n = $len>100 ? 8 : 2;
			while( strlen($gamma)<$len ){
				$gamma .= substr(pack('H*', sha1($passw.$gamma.$salt)), 0, $n);
			}
			return $str^$gamma;
		} //Encode decode string by pass


	}


