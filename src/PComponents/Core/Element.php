<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Element - The base Class componen element not Component entry point or manager
 *              (getModel, getTest, call, ...)
 *
 * @author sergey
 */

namespace PComponents\Core;

class Element extends Element\Base
{

    protected $initParams = array();
    protected $path = '';
    protected $defaultExt = '.twig';
    protected $tplEngin;
    protected $component = null;
    public $c;  // diContainer

    public function __construct($component, $path,
                                Element\ObjectCacher $cache)
    {
        $this->component = $component;
        $this->manager   = $component;
        $this->cname     = $this->component->cname;

        parent::__construct($path, $cache);

        $this->initParams['tplEnginName'] = 'twig';
    }

    // function to be called on object creation
    // non need to use __constructor and call parent
    public function init()
    {
        
    }

    public function initEl($c, array $params) // init Element
    {
        $this->c = $c;
        if (isset($this->c[$params['tplEnginName']])) {
            $this->tplEngin = $this->c[$params['tplEnginName']];
        } else {
            $this->tplEngin = new Obj\NullObj();
        }
        $this->init();
    }

    /**
     *  Call a Components Controller Action As Command 
     * call( {name} , [action | $param[0],] [$param[...], ] ....)
     *     -> name->action(array $params); // $params is numeric Array
     * 
     * @cont type name - Desc 
     * @action type name - 
     * 
     * @return type - Desc     
     */
    public function call($name)
    {
        $this->unsetError();

        $obj = $this->getObject('controllers', $name);

        $args = func_get_args();

        array_shift($args); // get rid of $name parameter

        $action = 'index';
        if(isset($args[0])){
            $action = $args[0];
        }


        if (!empty($action) && is_callable(array($obj, $action))) {
            array_shift($args);
            if (count($args) === 1) {
                return $obj->$action($args[0]);
            } else {
                return $obj->$action($args);
            }
        } 

        return null;
    }

    /**
     *  get the Component Model as object 
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function getModel($name)
    {
        $this->unsetError();
        
        $obj = $this->getObject('models', $name);

        if (is_object($obj)) {
            return $obj;
        } else {
            var_dump(array('not an object model ' . $name, $obj, $this->cache));
            exit;
        }
    }

    /**
     *  get Test File
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function getTest($name)
    {
        $this->unsetError();
        $obj = $this->getObject('tests', $name);

        if (is_object($obj)) {
            return $obj;
        } else {
            var_dump(array('not an object test   ' . $name, $obj, $this->cache));
            exit;
        }
    }

    /**
     *  get Test File
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function getNonElementObject($type, $name)
    {
        $this->unsetError();
        $obj = $this->getObject($type, $name);

        if (is_object($obj)) {
            return $obj;
        } else {
            var_dump(array('not an object ' . $name, $type, $obj, $this->cache));
            exit;
        }
    }

    /**
     *  Get View file contents - to be used in template engine  
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function view($name, $ext = '.html')
    {
        $this->unsetError();
        $obj = $this->getObject('views', $name, $ext);
        if (empty($obj)) {
            $obj = $this->getObject('views', $name, '.phtml');
        }

        return $obj;
    }

    public function html($name, $ext = '.html')
    {
        return $this->view($name . $ext);
    }

    public function renderTpl($name, array $params = array())
    {
        $ext = $this->defaultExt;

        $f_name = $this->getComponentsFileName('views', $name, $ext);

        return $this->tplEngin->render($f_name, $params);
    }

    /**
     *  Render PHP template && return a result  
     * 
     * @param string $name - name of the template 
     * @param string $ext - an extention of the template 
     * @param array  $params - an optional parameters 
     * 
     * @return type - Desc     
     */
    public function viewRender($name, $ext = '.phtml', $params = array(),
                               $toCache = true)
    {

        if (!is_string($ext)) {
            if (empty($ext)) {
                $ext = '.phtml';
            } elseif (\PComponents\Tools\ArrayTools::isAssocFull($ext)) {
                if (is_bool($params)) {
                    $toCache = $params;
                }
                $params = $ext;
                $ext    = '.phtml';
            }
        }


        //if($name == 'listing___collection' || $name == 'main___jeans')        var_dump($params);

        $this->unsetError();
        $obj = $this->getObject('views', $name, $ext, $params, $toCache);
        if (empty($obj)) {
            $obj = $this->getObject('views', $name, '.html', $params, $toCache);
        }



        return $obj;
    }

    

    public function getComponentData($key)
    {
        return @$this->component->local_data[$key];
    }

    public function setComponentData($key, $data)
    {
        $this->component->local_data[$key] = $data;
    }

}
