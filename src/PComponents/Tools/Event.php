<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Event
 *
 * @author sergey
 */

namespace PComponents\Tools;
	 
class Event{
    
    /* 
     * @var $evts - event function bindings collection 
     */
    private static $evts = array();
    
    
    /*
     * 
     */
    private static function forceArray($arr){
          if(is_array($arr)){
              return $arr;
          }
          return array($arr);
    }
    
    
    /*
     * Sends $evt - event into the collection
     * 
     * @param string $evt - the event code (name)
     * @param misc $info - the data to pass to the callbacks
     * 
     * @returns the array of answers of callbacks  
     */
    public static function dispatch($evt, &$info){
        $info = self::forceArray($info);
        $args = $info;
        $rets = array();
        if(isset(self::$evts[$evt])){
            foreach(self::$evts[$evt] as $key=>$listener){
                // collect all the ansawers into array 
                $rets[$key] = call_user_func_array($listener, $args);                
            }
        }
        $args['__returns'] = $rets; // store the aswers to the caller in the __returns variable
        
        return $args;
    }
    
    /*
     * // callback can me named or unnamed (only named callbacks can be removed)
     * @param string $evt - the event code (name)
     * @param callable $callback - the callback th be called
     * @param string $name - (optional) the name of the service if result is to be collected or be able to remove the callback
     * 
     */    
    public static function addListener($evt, $callback, $name=null){ 
        if(!isset(self::$evts[$evt])){
            self::$evts[$evt] = array();
        }
        if($name === null){
            self::$evts[$evt][] = $callback;
        }else{
            self::$evts[$evt][$name] = $callback;
        }
    }
    
    /*
     * // only named callbacks can be removed
     * @param string $evt - the event code (name)
     * @param string $name - the name of the service to be collected
     * 
     */  
    public static function removeListener($evt, $name){  
        if(isset(self::$evts[$evt][$name])){
            array_splice(self::$evts[$evt], array_search($name, self::$evts[$evt]), 1);      
            return true;
        }else{
            return false;
        }
    }
}
	 

