<?php



error_reporting(E_ALL ^ E_STRICT);//we want all possible errors

class base
{
    private $extended_objects = array();
    protected $_parents = array();
    
    public function __construct() {
    
    }
    
    protected function _extends($class) { //the $class is put to enforce passing class name (otherwise class name can be ommited and no error would be rased)
        $this->_parents[] = $class;
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
        if ( $this instanceof $class || in_array($class, $this->_parents)) {
            return true;
        }
 
        foreach ($this->extended_objects as $parent) {
            if ($parent instanceof base) {
                if ($parent->isInstanceOf($class)) {
                    return true;
                }
            }
        }
 
        return false;
    }

}


class c1 extends base
{

    //protected static $_extends = array();
    
    public function __construct($test_arg) {
        parent::__construct();
        print $test_arg;
    }
    
    //test members
    private $c1_priv_var = 'c1_priv_var';
    protected $c1_prot_var = 'c1_prot_var';
    public $c1_pub_var = 'c1_pub_var';

    private function c1_priv_method() {
        print 'c1_priv_method';
    }

    protected function c1_prot_method() {
        print 'c1_prot_method';
    }
    
    public function c1_pub_method() {
        print 'c1_pub_method';
    }
    
    protected static function c1_static_prot_method() {
        print 'c1_static_prot_method';
    }
    
    public static function c1_static_pub_method() {
        print 'c1_static_pub_method';
    }
}

class c2 extends base
{
    protected static $_extends = array('c1');
    
    public function __construct() {
        parent::__construct();
        $this->_extends('c1','c2 constructor arg');
    }
    
    //test members
    public function c2_pub_method() {
        $this->c1_prot_method();//ok
        $this->c1_pub_method();//ok
    }
}
echo '<pre>';
$o = new c2;
echo PHP_EOL;
print $o->c1_pub_var;//OK
echo PHP_EOL;
print $o->c1_prot_var;//also works but should not (if it was a real implementation of multiple inheritance) as this is a protected variable
//print $o->c1_priv_var;//ok - it is expected to fail
echo PHP_EOL;
$o->c2_pub_method();//ok
echo PHP_EOL;
$o->c1_pub_method();//ok
echo PHP_EOL;
$o->c1_prot_method();//also works but should not (if it was a real implementation of multiple inheritance) as this is a protected variable
//$o->c1_priv_method();//ok - it is expected to fail
echo PHP_EOL;
var_dump($o->isInstanceOf('c1'));
var_dump($o->isInstanceOf('c2'));
echo PHP_EOL;
echo PHP_EOL;

//static
c2::c1_static_pub_method();//ok
echo PHP_EOL;
c2::c1_static_prot_method();//also works but should not (if it was a real implementation of multiple inheritance) as this is a protected variable

