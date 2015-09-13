<?php

Namespace Pwd\PTime;



use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;
use Adianti\Database\TRecord;
use Pwd\PTime\Thread;
use Exception;


class PTimer {

private $model;
private $database;
private $filter;
private $dormir;
private $tentar;

function __construct($database,$model,$filter,$try,$sleep = 6){


$this->filter = $filter;
$this->database = $database;
$this->model = $model;
$this->tentar = $try;
$this->dormir = $sleep;


}


public function create(){

$Thread = new Thread();

$Thread->Create(function(){

	


$result = true;
$loop = $this->tentar;

$linhas =  "";
$count = 0;
do{


try{

TTransaction::open("$this->database");

$criteria = new TCriteria();
$criteria->add($this->filter);

$repo = new TRepository("$this->model");

if($repo->count($criteria) > 0){

$linhas =  $repo->load($criteria);
$result = false;

}else{

$count++;
$linhas = 0;
}

if($count == $loop){

$result = false;

}
TTransaction::close();


}catch(Exception $e){

TTransaction::rollback();
}

sleep($this->dormir);

}while($result);

});

return $linhas;

}

}

?>