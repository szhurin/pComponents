<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Tools;

/**
 * Description of Path
 *
 * @author sergey
 */
class Path
{
    private $path;
    private $ensureTrailingSlash = true;
    
    public function __construct($path = null)
    {
        if(empty($path)){
            $path = getcwd() . '/';
        }
        $this->path = $path;
    }


    public function setTrailingSlash(bool $slash = null)
    {
        if($slash === null) $slash =true;
        $this->ensureTrailingSlash = $slash;
    }
    
    public function getTrailingSlash()
    {
        return $this->ensureTrailingSlash;
    }
    
    public function setPath($path)
    {
        if(is_string($path)){
            return $this->path = self::fixPath($path);
        }else{
            return false;
        }
        
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
   
    
    public function Up($path = null)
    {
        if(empty($path)){
            $dir = $this->path;
        }else{
            $dir = self::fixPath($path, true);
        }
        $tmp = $this->outPath($this->setPath(realpath($dir.'..')));
        return $tmp;
        //return $this->outPath($this->setPath(realpath($dir.'..')));
    }
    
    public function cd($name, $path = null)
    {
        if(empty($path)){
            $dir = $this->path;
        }else{
            $dir = self::fixPath($path, true);
        }
        
        return $this->outPath($this->setPath(realpath($dir.$name)));
    }
    
    
    public static function getCurrentDirName($path)
    {
        $tmp = explode('/', trim(str_replace('\'', '/', $path), '/ '));
        return array_pop($tmp);
    }
    
    public static function fixPath($path, $ensureTrailingSlash = true)
    {
        $path = str_replace('\\', '/', $path);
        $last = substr($path, -1);
        if($ensureTrailingSlash && $last !== '/'){
            return $path.'/';
        }elseif(!$ensureTrailingSlash && $last === '/'){
            return substr($path , 0, -1);
        }
        return $path;
    }
    
    private function outPath($noError= true)
    {
        if(empty($noError)){
            return false;
        }
        
        if(!$this->ensureTrailingSlash){
            return substr($this->path, 0, -1);
        }
        return $this->path;
    }
}
