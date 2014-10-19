<?php

namespace PComponents\Tools;

class ArrayTools 
{
    static protected $pReplaceFrontArr = array( '"' => '```',"'" => '``');
      
	static public function serialize($arr, $replace = false)
	{
        if($replace !== false){
            
            if(is_array($replace)){
                $repArr = $replace;
            } else {
                $repArr = self::$pReplaceFrontArr;
            }
            $vals = str_replace(array_keys($repArr),  array_values($repArr), implode('→',array_values($arr)));
        }else{
            $vals = implode('→',array_values($arr));
        }
		$res = (!isset($arr) || gettype($arr) != 'array') ? '' :   implode('→',array_keys($arr)) . '↔' . $vals;
        return $res;
	}
	
	static public function unserialize($str, $replace = false)
	{
		if  (gettype($str) != 'string') return array();
		$tmp = explode('↔',$str, 2);
        
         if($replace !== false){
            
            if(is_array($replace)){
                $repArr = $replace;
            } else {
                $repArr = self::$pReplaceFrontArr;
            }
            $tmp[1] = str_replace(array_values($repArr),  array_keys($repArr), $tmp[1]);
        } 
        
		return (!isset($tmp[1])) ? array() : @array_combine(explode('→',$tmp[0]), explode('→',$tmp[1]));
	}
    
    static public function isVector($array){
        
        return (is_array($array))?array_values($array) === $array: false;
    }
	
    static function isAssoc($array) {
        return (is_array($array))? (bool)count(array_filter(array_keys($array), 'is_string')):false;
    }
    
    static function isAssocFull($array) {
        return (is_array($array) && !empty($array))? count(array_filter(array_keys($array), 'is_string')) === count($array) : false;
    }
    
    
}
