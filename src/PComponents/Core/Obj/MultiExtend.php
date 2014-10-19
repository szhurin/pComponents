<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Core\Obj;

/**
 * Description of MultiExtend
 *
 * @author sergey
 */
class MultiExtend
{
    private $extended_objects = array();
    
    public function __construct() {
    
    }
    
    protected function _extends($class) { //the $class is put to enforce passing class name (otherwise class name can be ommited and no error would be rased)
        $args = func_get_args();
        $class = array_shift($args);
        $reflection_object = new \ReflectionClass($class);//reflection has to be used so arguments can be provided to the constructor
        $this->extended_objects[$class] = $reflection_object->newInstanceArgs($args);
    }
    
    public function __get($property) {
        foreach ($this->extended_objects as $object) {
            if (isset($object->$property)) {
                return $object->$property;
            }
        }
        //it is good to be strict...
        throw new \Exception(sprintf('Trying to get %s property on object of class %s.',$property,get_class($this)));
    }
    
    public function __set($property,$value) {
        foreach ($this->extended_objects as $object) {
            if (isset($object->$property)) { //variable variable
                $object->$property = $value;
                return;
            }
        }
        //it is good to be strict...
        throw new \Exception(sprintf('Trying to set %s property on object of class %s.',$property,get_class($this)));
    }
    
    public function __isset($property) {
        foreach ($this->extended_objects as $object) {
            if (isset($object->$property)) {
                return true;
            }
        }
        return false;
    }
    
    public function __unset($property) {
        //it is good to be strict...
        throw new \Exception(sprintf('Trying to unset %s property on object of class %s. In strict classes unsetting is nto allowed.',$property,get_class($this)));
    }
    
    public function __call($method,$args) {
        foreach ($this->extended_objects as $object) {
            if (method_exists($object,$method)) {
                return call_user_func_array(array($object,$method),$args);
            }
        }
        throw new \Exception(sprintf('Dynamic call of unexistant method %s on instance of class %s.',$method,get_class($this)));
    }
    
    public static function __callStatic($method,$args) {
        $class = get_called_class();//late static binding
        if (isset($class::$_extends)) { //then there is static extension
            foreach ($class::$_extends as $extended_class) {
                if (method_exists($extended_class,$method)) {
                    return call_user_func_array(array($extended_class,$method),$args);
                }
            }
        }
        throw new \Exception(sprintf('Static call of unexistant method %s on instance of class %s.',$method,$class));
    }
    
    public function __invoke() {
        $args = func_get_args();
        foreach ($this->extended_objects as $object) {
            if (method_exists($object,'__invoke')) {
                //return $object();
                return call_user_func_array(array($object,'__invoke'),$args);
            }
        }
        throw new \Exception(sprintf('Invoking an instance of %s as function.',get_class($this)));
    }
    
    public function isInstanceOf($class)
    {
        if (in_array($this->_parents, $class)) {
            return true;
        }
 
        foreach ($this->extended_objects as $parent) {
            if ($parent instanceof MultipleInheritance) {
                if ($parent->isInstanceOf($class)) {
                    return true;
                }
            }
        }
 
        return false;
    }

}
