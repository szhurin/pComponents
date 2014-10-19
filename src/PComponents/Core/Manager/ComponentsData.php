<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ComponentsData - Manager for components Data - Exports ... * 
 * 
 * @author sergey
 */
namespace PComponents\Core\Manager;

abstract class ComponentsData extends Base
{
    public function registerDirectory($path)
    {
        $components = array();

        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (strpos($entry, '.') === false && $entry !== 'Tests' && $entry !== 'Menu') {
                    $class = '\\App\\Components\\' . $entry . '\\' . $entry . 'Component';
                    $this->containerManager->registerComponents(array(
                        new $class
                    ));
                }
            }
            closedir($handle);
        }
    }
    
    public function updateComponentDirectory($path)
    {
        $components = array();
        
        $path = $this->fixPath($path);
        
        if(!file_exists($path)){
            return false;
        }

        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (strpos($entry, '.') === false) {
                    
                    
                    
                    $obj = $this->getComponentObject($path.'/'.$entry);
                    if(empty($obj)){
                        var_dump(['no Component', $path.'/'.$entry]);
                        continue;
                    }
                    $objects = $this->registerComponents(array(
                        $obj
                    ));
                    
                    $reg_obj = $objects[0];
                    $reg_obj->updateCacheExports();
                    
                }
            }
            closedir($handle);
        }
        return true;
    }
    /**
     * 
     * @param string $componentDir - the path ro component
     * 
     * @return \PComponents\Core\Component - the obj 
     */
    public function getComponentObject($componentDir)
    {
        $path = $this->fixPath($componentDir);
        $parts = explode('/', $path);
        $name = array_pop($parts);
        if(empty($name)){
            $name = array_pop($parts);
        }
        
        $fname = $path. '/'.$name.'Component.php';
        if(!is_file($fname)){
            return false;
        }
        
        
        $obj_code = file_get_contents($fname);
        
        $obj_code_parts = explode('namespace', $obj_code);
        
        if(count($obj_code_parts)<2){
            return false;
        }
        $namespace = trim(strstr($obj_code_parts[1],';', true));
        
         
        $cname = '\\'. $namespace .'\\'.$name.'Component'; 
        if(!class_exists($cname)){
            include $fname;
            if(!class_exists($cname)){
                return false;
            }
        }
        $obj = new $cname;
        
        return $obj;
    }
}