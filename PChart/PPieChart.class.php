<?php

/**
 * PEntry Widget
 *
 * @version    3.0
 * @package    PWD
 * @subpackage form
 * @author     Alexandre Evangelista de Souza
 *@ description PieChart
 */
 
 
 class PPieChart extends TElement{
 
 private $cart;
 private $data;
 private $pieHole;
 private $title;
 private $size;
 private $is3D;
 
 
 
 public function __construct($title,$is3D = false){
 
   parent::__construct('div');


        $this->id = 'piechart'.uniqid();
        $this->title = $title;
        $this->is3D = $is3D;
        $this->data = "";
        
        
 } 
 
 public function setSize($width,$height){
 
 $this->size =  "width: ".$width."px; height: ".$height."px;";
 
 }
 
 public function setData($data){
 
 $n = count($data);
 $i = 1;
 
 foreach($data as $nome => $valor):
 
 if($i < $n){
     $this->data .= "['$nome',$valor],";
  }
 else{
     $this->data .= "['$nome',$valor]";
  }
  $i++;
  
  endforeach;
 }
 
 
 public function show(){
 

 $options = ($this->is3D?"is3D:true,":" pieHole: 0.4,");
 
 
 
 
 TScript::create("
  google.charts.load('current', {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
        ['Progs', 'Chart'],
           $this->data
                    
        ]);

        var options = {
           title: '$this->title',
          $options
          
        };

        var chart = new google.visualization.PieChart(document.getElementById('$this->id'));
        chart.draw(data, options);
      }
 
 
 
 
 ");
 
 parent::show();
 }
 }