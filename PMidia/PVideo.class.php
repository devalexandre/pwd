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
 
 
class PVideo extends TElement{

 protected $sources;
 protected  $content;


    public function __construct($width,$heigth)
    {
        parent::__construct('video');


        $this->id = 'video'.uniqid();
        $this->width = $width;
        $this->heigth = $heigth;
        $this->controls = true;
        
        $this->sources = array();
        
}

/**
* camonho do video
*@param $source /video/video.mp4
*formato do video
*@param $type video/mp4 , video/ogg
*/
public function addSource($source,$type){

$video = new TElement('source');
$video->src = $source;
$video->type = $type;

$this->sources[] = $video;

}


public function show(){

foreach($this->sources as $s):

parent::add($s);

endforeach;

parent::show();

}
}
?>