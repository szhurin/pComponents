<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  BaseObj - base functions for ERROR system | Messaging System | namespace 
 *
 * @author sergey
 */
namespace PComponents\Core\Obj;

use PComponents\Tools as Tools;

class Base
{
    /**
     * diManager Obj
     * @var \PComponents\Core\Manager  
     */
    protected $manager = null; 
    
    public $cname = '';
    
    private $errors;
    
    
    public function __construct()
    {
        $this->errors = new Tools\DataCollection();
        
    }
    
    
    /**
     *  Add Listener to the Event System   
     * 
     * @param string $event - String Name of the event to dispach the message to 
     * @param array  $args  - An Arrage of arguments to pass to the callback 
     * 
     * @return type - ther changed Args array + __returns = array of returned fields     
     */
    protected function send($event, $args=array())
    {
        $tmp = Tools\Event::dispatch($event, $args);        
        
        return $tmp;
    }
    
    
    /**
     *  Add Listener to the Event System hook list   
     * 
     * @param string    $event    - String Name of the event to attach to 
     * @param callable  $callback - A Callback to register to given event 
     * @param string    $name     - An Optional name of the callback (ou can recieve ansver from the called listeners, 
     *                              Only named callback can be removed in the future) 
     * 
     * @return type - none     
     */
    protected function addListener($event, $callback, $name= null)
    {
        Tools\Event::addListener($event, $callback, $name);
        return;
    }
    
    /**
     *  Delete Listener from the Event System hook  
     * 
     * @param string    $event    - String Name of the event to delete from 
     * @param string    $name     - Name of the callback to delete (Only named callback can be removed) 
     * 
     * @return type - true on success.    
     */
    protected function delListener($event, $name= null)
    {
        return Tools\Event::removeListener($event, $callback, $name);        
    }    
    
    
    
    
    protected function fixPath($path)
    {
        $path = str_replace('\\', '/', $path);
        if(substr($path, -1) === '/'){
            $path = substr($path, 0, -1);
        } 
        return $path;
    }
    
    
    /**
     * 
     * @param string $str - string to convert
     * @param string $dash_str - the type of string to us as lowdash
     * @return string
     */
    public function lodashToCamel($str, $dash_str='_')
    {
        return Tools\TextConverter::lodashToCamel($str, $dash_str);        
    }
    /**
     * 
     * @param string $str - string to convert
     * @param bool $skipfirst - true if no need test first char for capital case
     * @param string $dash_str - the type of string to us as lowdash
     * @return string
     */
    public function camelToLodash($str, $skipfirst = true, $dash_str='_')
    {
        return Tools\TextConverter::camelToLodash($str, $skipfirst, $dash_str);
    }
    
    public function reloadTo($path)
    {
        header('Location: '.$path);
        exit;
    }
    
    
    
    protected function unsetError()
    {
        $this->errors->unsetEntries();     
    }
    public function getError()
    {
        return  $this->errors->getEntry();
    }
    protected function setError($text, $code = 1)
    {
        $this->errors->setEntry($text, $code);
    }
    
    
    public function array_to_object(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->array_to_object($value);
            }
        }
        return (object) $array;
    }
}
