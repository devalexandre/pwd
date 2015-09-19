<?php

/**
 * PCepProgs 
 *
 * @version    1.0
 * @version adianti framework 2.0
 * @package    PTimer
 * @author     Alexandre E. Souza
 
 */
 

class PTimer {

private $banco;
private $model;
private $action;
private $time;
private $campos;
private $key;

/**
@param $banco banco a ser usado
@param  $model model a ser usada
@param  $campos  campos a serem exibidos
@param  $action type array (controller,action)
@param $key  key da action 
@param  $time tempo para reexecutar o timer

*/
function __construct($banco,$model,$campos,$action,$key,$time){


$this->banco = $banco;
$this->model = $model;
$this->action = $action;
$this->time = $time;
$this->campos = $campos;
$this->key = $key;

}





public function show(){


$time = new TElement('div');

$time->id = "ptimer_".uniqid();

$campos = serialize($this->campos);
$action = serialize($this->action);//$campos = base64_encode($campos);


if(!file_exists('timer.php')){

$file = file_get_contents('app/lib/PWD-Lib/PTimer/timer.php');
$fp = fopen("timer.php", "a");
 

$escreve = fwrite($fp, "$file");
 
// Fecha o arquivo
fclose($fp);
}
$url = "'timer.php'";


$code = "

runPtimer($url,'$this->model','$this->banco','$campos','$action','$this->key',$this->time,'$time->id');

";

 TScript::create($code);
 



$time->show();

}



}



?>