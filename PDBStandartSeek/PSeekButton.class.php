<?php

class PSeekButton extends TEntry implements AdiantiWidgetInterface
{
    private $action;
    private $auxiliar;
    private $useOutEvent;
    private $button;
    protected $formName;
    protected $name;
    
   // new TSeekButton
    
    /**
     * Class Constructor
     * @param  $name name of the field
      * @param  $model name of the Model
       * @param  $form name of the Form
        * @param  $database name of the Database
         * @param  $filtros name of the field of filters (Array)
          * @param  $fields name of the fields of columns
     */
    public function __construct($name,$model,$form,$database,$filtros,$fields)
    {
        parent::__construct($name);
        $this->useOutEvent = TRUE;
        $this->setProperty('class', 'tfield tseekentry', TRUE);   // classe CSS
        $image = new TImage('lib/adianti/images/ico_find.png');
        
        $this->button = new TElement('button');
        $this->button->{'class'} = 'btn btn-default tseekbutton';
        $this->button->{'type'} = 'button';
        $this->button->{'id'} = 'Pbutton_'.$name;
        $this->button->{'onmouseover'} = 'style.cursor = \'pointer\'';
        $this->button->{'name'} = '_' . $this->name . '_link';
        $this->button->{'onmouseout'}  = 'style.cursor = \'default\'';
        $this->button->add($image);
        
         $url ='index.php?class=PDBStandartSeek&method=onReload';
         
      
            $action = "__adianti_load_page( '{$url}');";
            $action.= "return false;";
          
              $this->button-> onclick   = $action;
   
      
      
        //configuração
        
TSession::setValue('Model',$model); // model
TSession::setValue('form',$form); //form de retorno
TSession::setValue('database',$database);
TSession::setValue('filtros',$filtros);
TSession::setValue('fields',$fields);


  
  
  
    }
    
    /**
     * Returns a property value
     * @param $name     Property Name
     */
    public function __get($name)
    {
        if ($name == 'button')
        {
            return $this->button;
        }
        else
        {
            return parent::__get($name);
        }
    }
    
    /**
     * Define it the out event will be fired
     */
    public function setUseOutEvent($bool)
    {
        $this->useOutEvent = $bool;
    }
    
    /**
     * Define the action for the SeekButton
     * @param $action Action taken when the user
     * clicks over the Seek Button (A TAction object)
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
    }
    
    /**
     * Define an auxiliar field
     * @param $object any TField object
     */
    public function setAuxiliar(TField $object)
    {
        $this->auxiliar = $object;
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tseekbutton_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tseekbutton_disable_field('{$form_name}', '{$field}'); " );
    }
    
    
    /**
     * Show the widget
     */
    public function show()
    {
       
            
            $this->button->show();
      
            parent::show();
        }
    
}
