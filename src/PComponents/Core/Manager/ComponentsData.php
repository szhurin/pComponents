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

use \PComponents\Tools\Path;

abstract class ComponentsData extends Base
{

    public function getComponentNS($dir)
    {
        $path = Path::fixPath($dir);

        if (file_exists($path . '_pcd_settings.php')) {
            $settings = include($path . '_pcd_settings.php');
            if (isset($settings['ns'])) {
                return $settings['ns'];
            }
        }

        if (file_exists($path . 'Component.php')) {
            $cCode   = file_get_contents($path . 'Component.php');
            $codeArr = explode('namespace ',
                               $cCode,
                               2);
            if (count($codeArr) === 2) {
                $ns = trim(array_shift(explode(';',
                                               $codeArr[1],
                                               2)));

                return $ns;
            }
        }

        $cName = \PComponents\Tools\Path::getCurrentDirName($dir);

        return $cName;
    }

    public function registerDirectory($path)
    {
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot() && !$fileInfo->isDir()) {
                continue;
            } else {

                $fpath = $fileInfo->getPathname();
                $ns    = $this->getComponentNS($fpath);
                $fname = $fileInfo->getBasename();

                if (file_exists($fpath . '/' . $fname)) {
                    $class = '\\' . $nc . '\\' . $fileInfo->getBasename('.php');
                    if (class_exists($class)) {

                        $this->containerManager->registerComponents(array(
                            $class
                        ));
                    }
                }
            }
        }
    }

    public function updateComponentDirectory($path)
    {
        $components = array();

        $path = Path::fixPath($path);

        if (!file_exists($path)) {
            return false;
        }

        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot() && !$fileInfo->isDir()) {
                continue;
            } else {


                $cname = $fileInfo->getBasename();
                
                if(empty($cname)){
                    continue;
                }
                
                $obj = $this->getComponentObject($path . '/' . $cname);
                if (empty($obj)) {
                    var_dump(['no Component', $path . '/' . $cname]);
                    continue;
                }
                $objects = $this->containerManager->registerComponents(array(
                    $obj
                ));

                $reg_obj = $objects[0];
                $reg_obj->updateCacheExports();
            }
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
        $path = Path::fixPath($componentDir);
        $name = Path::getCurrentDirName($path);

        $fname = $path . $name . 'Component.php';
        if (!is_file($fname)) {
            return false;
        }
        
        
        if (!is_file($path . 'Component.php')) {
            var_dump('ComponentsData including ', $path. 'Component.php');
            include($path . 'Component.php');
            
        }else{
            var_dump('ComponentsData NOT including ', $path. 'Component.php');
        }
        include_once($fname);
        $namespace = $this->getComponentNS($componentDir);

        var_dump(' class '. '\\' . $namespace . '\\' . 'Component' , 
                class_exists( '\\' . $namespace . '\\' . 'Component'));

        $cname = '\\' . $namespace . '\\' . $name . 'Component';
        if (!class_exists($cname)) {
            return false;
        }
        $obj = new $cname;

        return $obj;
    }

}
