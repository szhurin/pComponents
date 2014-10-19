<?php
/**
 *  Element Code Cacher 
 *
 * @author sergey
 */
namespace PComponents\Core\Element;

class ObjectCacher
{
    private $data = array();
    
    public function __construct(array $array= array()) {
        $this->data = $array;
    }
    
    public function check($type, $name)
    {
        return isset($this->data[$type][$name]);
    }
    
    public function set($type, $name, $obj)
    {
        $this->data[$type][$name] = $obj;
    }
    
    public function delete($type, $name)
    {
        unset($this->data[$type][$name]);
    }
    
    public function get($type, $name)
    {
        return $this->data[$type][$name];
    }   
    
    
}