<?php



/**
 * PEntry Widget
 *
 * @version    3.0
 * @package    PWD
 * @subpackage PMidia
 * @author     Alexandre Evangelista de Souza
 *
 */
 

class PAudio extends TElement{

 protected $sources;
 protected  $content;


    public function __construct()
    {
        parent::__construct('audio');


        $this->id = 'audio'.uniqid();
       
        $this->controls = true;
        
        $this->sources = array();
        
}

/**
* camonho do video
*@param $source /audio/audio.mp3
*formato do video
*@param $type video/mp4 , video/ogg
*/
public function addSource($source,$type){

$audio = new TElement('source');
$audio->src = $source;
$audio->type = $type;

$this->sources[] = $audio;

}


public function show(){

foreach($this->sources as $s):

parent::add($s);

endforeach;

parent::show();

}
}
?>