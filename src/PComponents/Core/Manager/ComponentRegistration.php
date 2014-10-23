<?php

namespace PComponents\Core\Manager;

use PComponents\Tools\Path;

/**
 * Description of ComponentRegistration
 *
 * @author sergey
 */
abstract class ComponentRegistration extends ServiceData
{

    private $componetRegistry = array();    // the array  componentName => component
    private $serviceCollection = array();

    /**
     *  registers the array of component directories into container 
     * 
     * @param array $paths - an array of component directories 
     * 
     * @return bool - true if all $paths are registered, false id error occured     
     */
    public function registerComponentDirectories(array $paths)
    {
        foreach ($paths as $path) {
            $dir    = Path::fixPath($path);
            $sfname = $dir . '_pcd_services.php';
            if (!is_file($sfname)) {
                $this->updateComponentDirectory($path);
            }
            $services = include_once $sfname;

            $common = array_intersect_assoc($this->serviceCollection,
                                            $services);
            if (!empty($common)) {
                throw new \PComponents\Exceptions\ServiceRegistrationException(
                'The services ' . implode(':',
                                          array_keys($common)) .
                ' are redefined in ' . implode(':',
                                               array_values($common)),
                        1
                );
            }
            
            $this->serviceCollection = array_merge($this->serviceCollection, $services);
        }
    }
    
    public function registerService($sname)
    {
        if(!$this->isServiceInCollection($name)){
            return FALSE;
        }
        $cname = $this->isServiceInCollection($name);
        
        if(class_exists($cname)){
            $this->registerComponents(array($cname));
            return true;
        }
        return false;
        
    }
    
    public function isServiceInCollection($sname)
    {
        return isset($this->serviceCollection[$sname]);
    }

    /**
     *  registers the array of components with container 
     * 
     * @param array $components - an array of component objects 
     * 
     * @return none     
     */
    public function registerComponents(array $components)
    {
        $objects           = array();
        $alreadyRegistered = array();
        foreach ($components as $key => $comp) {
            if (is_string($comp) && is_subclass_of($comp,
                                                   '\PComponents\Core\Component')) {
                $obj = new $comp;
            } elseif (is_subclass_of($comp,
                                     '\PComponents\Core\Component')) {
                $obj = $comp;
            } else {

                throw new \PComponents\Exceptions\ComponentRegistrationException(
                'not a valid input for Component registration in ' . $key . ' place in array.' .
                ' Should be ether string (class name) or the instance of Component.' .
                ' Given ' . ((is_string($comp)) ? ' a string: ' . $comp : get_class($comp)));
            }

            if (!$this->isComponentRegistered($obj->cname)) {
                if (is_callable(array($obj, 'baseAttach'))) {
                    $obj->baseAttach($this->c,
                                     $this);
                }
                $objects[]                           = $obj;
                $this->componetRegistry[$obj->cname] = $obj; // save components in array
            } else {// already have the object of this type
                $objects[]           = $this->componetRegistry[$obj->cname];
                $alreadyRegistered[] = $obj->cname;
            }
        }

        foreach ($objects as $obj) {
            if (in_array($obj->cname,
                         $alreadyRegistered)) {
                continue;
            }
            if (!$this->isComponentRegistered($obj->cname)) {
                if (is_callable(array($obj, 'baseInit'))) {
                    $obj->baseInit($this->c,
                                   $this);
                }
            }
        }
        return $objects;
    }

    public function isComponentRegistered($cname)
    {
        return (isset($this->exports[$cname]));
    }

    public function isServeceRegistered($name)
    {
        return (isset($this->serviceList[$name]));
    }

    public function getRegisteredComponents(array $names)
    {
        $return      = array();
        $to_register = array();
        $wrong_names = array();
        foreach ($names as $name) {

            if (substr($name,
                       0,
                       1) !== '\\') {
                $name = '\\' . $name;
            }

            if (!$this->isComponentRegistered($name)) {

                try {
                    $obj = new $name;
                } catch (Exception $e) {
                    $wrong_names[] = $name;
                }
                if (is_object($obj)) {
                    $to_register[] = $obj;
                    $return[$name] = $obj;
                }
            } else {
                $return[$name] = $this->componetRegistry[$name];
            }
        }

        $this->registerComponents($to_register);

        return $return;
    }

}
