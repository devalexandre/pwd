<?php
/**
 * AlunosList Listing
 * @author  <your name here>
 */
class PDBStandartSeek extends TWindow
{
     private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
     private   $filtros;
     private  $fields ;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::setSize(500,400);
        parent::setTitle('Teste');
        
        $this->filtros = TSession::getValue('filtros');
         $this->fields = TSession::getValue('fields');
        
        // creates the form
        $this->form = new TForm('form_search_Alunos');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel(TSession::getValue('Model') )->colspan = 2;
        

        // create the form fields
     
        $campos = array();
        foreach($this->filtros as $f):
        ${"$f"} = new TEntry("$f");
       $campos[] = ${"$f"};
       endforeach;

      
foreach($this->filtros as $f):

        // add one row for each form field
        $table->addRowSet( new TLabel($f.':'),${"$f"});
     
     endforeach;


        $this->form->setFields($campos);


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('filter_data') );
        
        // create two action buttons to the form

      
       $find_button = TButton::create(_t('Find'), array($this, 'onSearch'), _t('Find'),'bs:search blue');
      
        
        $this->form->addField($find_button);

        
        $buttons_box = new THBox;
        $buttons_box->add($find_button);
       
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
       
         foreach($this->fields as $c => $label):
        ${"$c"}   = new TDataGridColumn("$c","$label", 'left', 200);
        endforeach;


        // add the columns to the DataGrid
          foreach($this->fields as $c => $label):
        $this->datagrid->addColumn(${"$c"});
         endforeach;

        
   // create the datagrid actions
    
        
        $delete_action = new TDataGridAction(array($this, 'onSelect'));
        $delete_action->setLabel('Select');
        $delete_action->setImage('bs:plus greem');
        $delete_action->setField('id');
        
       
         
        // add the actions to the datagrid
        $this->datagrid->addAction($delete_action);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create the page container
        $container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        parent::add($container);
    }
    
  
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        foreach($this->filtros as $f):
        TSession::setValue('filter_'.$f,   NULL);
      
        endforeach;
        
      

foreach($this->filtros as $f):

        if (isset($data->{"$f"}) AND ($data->{"$f"})) {
            $filter = new TFilter("$f", 'like', "%{$data->$f}%"); // create the filter
            TSession::setValue('filter_'.$f,   $filter); // stores the filter in the session
            
           
        }

  endforeach;

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            
            // creates a repository for Alunos
            
            $repository = new TRepository(TSession::getValue('Model'));
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
             foreach($this->filtros as $f):

            if (TSession::getValue('filter_'.$f)) {
                $criteria->add(TSession::getValue('filter_'.$f)); // add the session filter
            }

             endforeach;

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onSelect($param)
    {
       try{
       TTransaction::open(TSession::getValue('database'));
       
       $class = TSession::getValue('Model');
       
       $data = new $class($param['key']);
       
       $obj = new stdClass();
       foreach($this->fields as $c => $label):
       $campo = $class."_".$c;
       
       $obj->{"$campo"} = $data->{"$c"};
       
       endforeach;
       
       TForm::sendData(TSession::getValue('form'),$obj);
       
       parent::closeWindow();
       
       }catch(Exception $e){
       
       new TMessage('error',$e->getMessage());
       }
    }
    

    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
   
 }