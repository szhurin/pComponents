<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ManagerBase - Export - import | ServiceList
 *
 * @author sergey
 */
namespace PComponents\Core\Manager;

abstract class Base extends \PComponents\Core\Obj\Base
{
    protected $exports = array();
    protected $imports = array();
    
    
    
    
    /**
    *  registers export with container 
    *  & adds Exports in exports array   
    * 
    * @param str $name - name of the service 
    * @param mics $data - the object to write to the container 
    * 
    * @return void - null     
    */
    protected function setExport($name, $data)
    {
        if(!isset($this->c[$name]) || !is_object($data)){
            
            $this->c[$name] = $data;
            
            array_push($this->exports, $name);
            
            $this->manager->addExport($name, $this); 
        }  else {
            var_dump([$this->cname, 'noreg', $name]);
        }      
    }

    /**
     *  safely gets a container variable (a component Service)   
     * 
     * @param str $name - Name of the needed Service 
     * 
     * @return mics - resulting service result or null if error 
     */
    public function getImport($name)
    {
        try{
            $res = $this->c[$name];
            array_push($this->imports, $name);
            $this->manager->addImport($name, $this); 
            
        } catch (Exception $ex) {
            $res = null;
            $this->setError('No Import found ' . $ex->getMessage(), 10);
        }       

        return $res;
    }
    
   
    /**
     *  get exports array
     *  
     * @return array - the array of services that were exported by the Component 
     */
    public function getExports()
    {
        return $this->exports;
    }
    
    /**
     *  get imports array
     *  
     * @return array - the array of services that were imported by the Component 
     */
    public function getImports()
    {
        return $this->imports;
    }    
    
    
    /**
     *  Able to register components from any Element
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function registerComponents(array $components)
    {
        return $this->manager->registerComponents($components);
    }
}