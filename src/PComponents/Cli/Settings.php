<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Cli;

/**
 * Description of settings
 *
 * @author sergey
 */
class Settings
{
    private $settingFileName = '_pcd_settings.php';
    private $defaultSettings = array('ns'=>'');
    private $settings = array('ns'=>'');
    
    
    public function findFSSettings($dir, $settingFileName= null)
    {
        if(empty($settingFileName)){
            $settingFileName = $this->settingFileName;
        }
        $path = new \PComponents\Tools\Path($dir);
        $cur_path = $path->getPath();
        // go dows until find or root directory =>  no components in the root
        
        $ns_path = '';
        
        
        while(!is_file($cur_path.$settingFileName) && $cur_path !== false ){
            $cur_dir = $this->getCurrentDirName($cur_path);
            $tmp = $path->Up();
            if($tmp !== $cur_path){
                $cur_path = $tmp;
                $ns_path = '/'. $cur_dir . $ns_path;
            }else{
                $cur_path = false;
            }
        }
        
        if(!$cur_path){
            $tmp_settings = $this->settings;
            
            $tmp_settings['__path'] = $cur_path;
            $tmp_settings['__ns_path'] = $ns_path;
            return $tmp_settings;
            
        }
        
        
        $this->settings = include($cur_path.$settingFileName);
        
        $this->settings['__path'] = $cur_path;
        $this->settings['__ns_path'] = $ns_path;
        
            
        return $this->settings;
    }
    
    public function getCurrentDirName($path)
    {
        $tmp = explode('/', trim(str_replace('\'', '/', $path), '/ '));
        return array_pop($tmp);
    }
    
}
