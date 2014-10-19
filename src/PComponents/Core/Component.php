<?php
/*
 * A component Base class
 */

/**
 * Component is a base class for An Entry point into the Component
 * extends Element and inits the initial Element
 * 
 * used to attach container && init component
 *
 * @author sergey
 */
namespace PComponents\Core;


class Component extends Component\Base
{        
    public $namespace;
     
    public function  __construct()
    {
        $this->cname = '\\'.get_class($this);
        
        $cache = new Element\ObjectCacher(array('controllers'=>array(), 'models'=>array(), 'views'=>array(), 'configs'=>array()));
        parent::__construct($this, $this->cname, $cache);
                
        //get fileName of the class construction the object - to know the 
        $class_info = new \ReflectionClass($this);
        $this->path = dirname($class_info->getFileName());
        
                
        // set Namespace and correct path
        $this->path = $this->fixPath($this->path) . DIRECTORY_SEPARATOR; 
        $this->namespace = '\\'.$class_info->getNamespaceName();
        
        
        $this->manager = $this;
        
    }    
        
    /**
    *  attach the component to the container
    * 
    * @param obj $c - a DI Container 
    * @param obj $manager - a Container manager 
    *     
    * @return void - null    
    */
    public function baseAttach($c, $manager)
    {
        $this->manager = $manager;
        
        $this->c =$c;
        $this->attach();
    }
    
    /**
    *  needs to be overritten by component entry point 
    * 
    * @return void - null     
    */
    public function attach() {  }
    
    /**
    * Init the component on the container
    * 
    * @return void - null    
    */
    public function baseInit()
    {
        $this->init();
    }
    /**
    *  needs to be overritten by component entry point 
    * 
    * @return void - null     
    */
    public function init() {  }
    
    
    
    /*
    public function __call(){
        
    } 
    
    public function __get(){
        
    }
    //*/
}
