<?php
namespace PComponents\Core\Manager;

/**
 * A Manager Class to work with ServiceList from all the Registered Components
 *
 * @author sergey
 */
abstract class ServiceData extends ComponentsData
{
    public $serviceList = array();
    public $importList = array();
    
    public $rewriteServiceList = array();
    
    
    
     /**
     *  DESC   
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function addImport($name, $obj)
    {
        $cname = $obj->cname;
        $this->imports[$cname][] =$name; 
        $this->importList[$name] = $cname;
        $this->manager->addImport($name, $obj); 

        return;
    }
    
    /**
     *  Adds an Object to the  
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function addExport($name, $obj)
    {
        $cname = $obj->cname;
        $this->exports[$cname][] =$name;
        
        if(isset($this->serviceList[$name])){ // log all rewrites of glogal services
            $this->rewriteServiceList[] = array($name=>$this->serviceList[$name]);
        }
        $this->serviceList[$name] = $cname;
        $this->manager->addExport($name, $obj); 
        
        return;
    }

}
