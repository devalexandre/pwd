<?php
include('init.php');

$banco = $_POST['banco'];
$model = $_POST['model'];
//$criterio = $_POST['criterio'];
$campos = $_POST['campos'];
$action = $_POST['action'];
$key = $_POST['key'];

$datagrid = new  TQuickGrid();

$fields = unserialize($campos);
$action = unserialize($action);

$criteria = new TCriteria();
$criteria->setProperty('limit',10);

$repo = new TRepository($model);



foreach($fields as $c):


$datagrid->addQuickColumn($c, $c, 'left', 200);
endforeach;

     // create the datagrid actions
  $timer_action = new TDataGridAction($action);
        
        // add the actions to the datagrid
        $datagrid->addQuickAction('action', $timer_action, $key, 'bs:search blue');



$datagrid->createModel();

try
{
    TTransaction::open($banco);
    $objects = $repo->load($criteria);
    
      if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $datagrid->addItem($object);
                }
            }
            
   
    $datagrid->show();
    
    TTransaction::close();
}
catch (Exception $e)
{
    print $e->getMessage();
}
?>