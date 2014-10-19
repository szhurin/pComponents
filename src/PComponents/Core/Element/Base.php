<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ElementBase - base function for ELEMENT Class (getObject, getFileList, writeFile, ...)
 *
 * @author sergey
 */

namespace PComponents\Core\Element;

use PComponents\Core\Manager as Manager;
//use PComponents\Core\Component as Component;

class Base extends Manager\Base
{

    protected $path        = '';
    /* @var $this->cache ElementCodeCacher */
    protected $cache       = array();
    protected $typesToRead = array('views');
    protected $elemetDirs  = array('models', 'controllers', 'tests');
    protected $fileSystem  = null;

    public function __construct($path, ObjectCacher $cache)
    {
        if (empty($path)) {
            $this->error = 'no PATH';
        } else {
            $path       = $this->fixPath($path) . '/';
            $this->path = $path;
        }
        $this->cache = $cache;
        parent::__construct();
    }

    public function getFileSystem()
    {
        if (is_null($this->fileSystem)) {
            $this->fileSystem = new \PComponents\Tools\FileSystem($this->path);
        }
        return $this->fileSystem;
    }

    protected function getObject($type, $fname, $ext = '.php', $params = false,
                                 $toCache = true)
    {
        $obj = null;

        $name = str_replace('__', '/', $fname);

        if ($this->cache->check($type, $name)) {
            $obj = $this->cache->get($type, $name);
        } else {

            $class = $this->includeFile($type, $name, $ext, $params);

            $tmpClass = $this->component->namespace . '\\' . $type . '\\' . str_replace(array(
                        '__', '/'), '\\', $name);

            if ($class === 1 && class_exists($tmpClass)) { // OBJECT
                $obj = new $tmpClass($this->component, $this->path, $this->cache);

                if (in_array($type, $this->elemetDirs)) {
                    $obj->initEl($this->c, $this->initParams);
                }

                if ($toCache) {
                    $this->cache->set($type, $name, $obj);
                }
            } elseif (!empty($class)) {  //VIEW
                $obj = $class;
                if ($toCache) {
                    $this->cache->set($type, $name, $obj);
                }
            } else {  // ERROR
                $tmp = $this->getError();
                $this->setError('no ' . $type . ' ' . $name . '  in ' . $tmp['text']);

                return false;
            }
        }



        return $obj;
    }

    protected function includeFile($type, $fname, $ext = '.php',
                                   $params = false, $repeate = false)
    {   
        $tmp = $this->getFileSystem()->getFullFileName($type, $fname, $ext);


        if (is_file($tmp)) {
            $str = $tmp;

            if ($ext === '.php' && $params === false) {
                $class = require_once $str;
            } else {
                if ($params !== false) {  // to render and return output from buffer
                    $varsToUnset = true;
                    if (is_array($params)) {
                        if (empty($this->vars)) {
                            $this->vars = $params;
                        } else {
                            $this->vars  = array_merge($this->vars, $params);
                            $varsToUnset = false;
                        }
                    } else {
                        //var_Dump($params);
                    }
                    ob_start();
                    include ($str);
                    $class = ob_get_clean();
                    if ($varsToUnset) {
                        unset($this->vars);
                    }
                } else {
                    $class = file_get_contents($str);
                }
            }

            return $class;
        } else {

            if (!$repeate) {
                $toRepeat = $this->resolveLocal($tmp);

                if ($toRepeat) {
                    return $this->includeFile($type, $fname, $ext, $params, true);
                }
            }

            $this->setError('no File in ' . $this->path . $tmp);

            return null;
        }
    }

    
    /**
     *   resolve file name for current Component from conponents base dir
     *
     *   @param type @type - resource type
     *   @param type @name - name of the resource
     *   @param type @ext  - file extention
     *
     *   @return string - global file name
     */
    public function getComponentsFileName($type, $fname, $ext)
    {
        $tmp = $this->getFileSystem()->getFullFileName($type, $fname, $ext);

        $compFilePath = str_replace('\\', '/', realpath($this->path . '../'));

        $res = trim(str_replace($compFilePath, '', $tmp), '/');

        return $res;
    }

    /**
     *   DESC
     *
     *   @param type @name - param desc
     *
     *   @return type - desc of return
     */
    public function configLocal($name, $feild = null)
    {
        $globalName = str_replace('local', 'global', $name);
        return ( $this->c['env'] === 'prod' ) ? $this->config($globalName,
                                                              $feild) : $this->config($name,
                                                                                      $feild);
    }

    /**
     *  Get php config file   
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function config($name, $feild = null)
    {
        $this->unsetError();
        $obj = $this->getObject('configs', $name);

        if (!empty($feild)) {
            if (isset($obj[$feild])) {
                return $obj[$feild];
            }
            return array();
        }
        return $obj;
    }

    /**
     *  Get php config file   
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function getDic($lang = null)
    {
        $this->unsetError();
        $langs = $this->c['lang.array'];
        if ($lang === null) {
            $lang = $this->c['lang'];
        } elseif (is_numeric($lang)) {
            if (isset($langs[$lang])) {
                $lang = $langs[$lang];
            } else {
                $lang = $this->c['lang'];
            }
        } elseif (!in_array($lang, $langs)) {
            $lang = $this->c['lang'];
        }


        $obj = $this->config('dictionary__' . $lang);

        if (empty($obj)) {
            return array();
        }

        return $obj;
    }

    /**
     *  DESC   
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function writeConfig($name, $data)
    {
        if (!is_array($data)) {
            $data = array('data' => $data);
        }

        if (substr($name, -4) !== '.php') {
            $name .= '.php';
        }

        return $this->getFileSystem()->writeFile('configs', $name,
                                                 '<?php ' . PHP_EOL . ' return ' . var_export($data,
                                                                                              true) . ';');
    }

    /**
     *  DESC   
     * 
     * @param type name - Desc 
     * 
     * @return type - Desc     
     */
    public function addToConfig($name, $data)
    {
        $oldData = $this->config($name);

        $tmp = array_merge_recursive($oldData, $data);

        return $this->writeConfig($name, $tmp);
    }

    /**
     *   resolve samples for local (gitignore) files
     *
     *   @param string @name - name of the file was unable to include 
     *
     *   @return bool - to try once more or exit with error 
     */
    public function resolveLocal($name)
    {
        //var_dump($name);
        if (strpos($name, '--samples') !== false) {
            return false;
        }

        $sname = str_replace(array($this->path), '', $name);

        $samples = $this->config('--samples');

        if (!is_array($samples)) {
            return false;
        }

        $res = array_search($sname, $samples);

        if (!$res) {
            return false;
        }





        $res = $this->path . $res;

        if (!file_exists($res)) {
            return false;
        }



        $newfile = $this->path . $sname;

        if (file_exists($newfile)) {
            return false;
        }

        if (!file_exists(dirname($newfile))) {
            mkdir(dirname($newfile), 0777, true);
        }



        if (!copy($res, $newfile)) {
            return false;
        }
        chmod($newfile, 0777);
        return true;
    }

}
