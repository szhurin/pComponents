<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Tools;

/**
 * FileSystem
 *
 * @author sergey
 * 
 * 
 */
class FileSystem
{

    public $path;

    /**
     * This is a valid docblock.
     *
     * @param string $path - the directory path to associalte FS with
     *
     */
    public function __construct($path)
    {
        if (substr($path, -1) !== '/') {
            $path .='/';
        }

        $this->path = $path;
    }

    /**
     *   resolve global file name for current Component
     *
     *   @param type @type - resource type
     *   @param type @name - name of the resource
     *   @param type @ext  - file extention
     *
     *   @return string - global file name
     */
    public function getFullFileName($type, $fname, $ext)
    {
        $tmp = $this->path . str_replace('__', '/', $type . '/' . $fname);

        $ext_length = strlen($ext);

        if (substr($tmp, -1 * $ext_length) !== $ext) {
            $tmp .= $ext;
        }
        if ($type == 'dictionary') {
            //var_dump($type, $fname, $tmp);
        }
        return $tmp;
    }

    /**
     *  Writes - Text to file located in $this->path . / .  $type . / . name  
     * 
     * @param string $type - the type of the file (directory in the PATH) 
     * @param string $name - the name of the file 
     * @param string $text - the tex to write to the file 
     * 
     * @return int - # of bytes written to file or false if not success
     */
    public function writeFile($type, $name, $text)
    {
        $tmp = str_replace('__', '/', $type . '/' . $name);

        $this->ensureDir($this->path . $tmp);

        $fileName = $this->path . $tmp;
        $toChmod  = !file_exists($fileName);

        $res = file_put_contents($fileName, $text . PHP_EOL);
        if ($toChmod && $res > 0) {
            chmod($fileName, 0777);
        }

        return $res;
    }

    /*
     * ensures the directory is exists if not creates it and set availability
     * 
     * @param string $path - the path to check for existence of directory
     * @param bool $isFile - is there a file in the path string 
     * @param int $mod - the mod to set if new dir is created  
     * 
     */

    public function ensureDir($path, $isFile = true, $mod = 0777)
    {
        $dirname = $path;
        if ($isFile) {
            $dirname = dirname($dirname);
        }

        if (!file_exists($dirname)) {
            try{
                mkdir($dirname, $mod, true);
                chmod($dirname, $mod);
            }  catch (Exception $e){
                throw new \Exception('cannot create or chmod of '. $dirname . ' in '. $e->getFile().' at line '. $e->getLine .' message '. $e->getMessage());
            }
            
        }
        return true;
    }

    /*
     * gets the file list from directory in the path
     * 
     * @param string $subDir - the directory where to look for files
     * @param bool $recurcive - to look recurcively 
     * @param string $origSubDir - should be empty, to look recurcively 
     * 
     */

    public function getFileList($subDir, $recurcive = true, $origSubDir = null)
    {
        $tmp = str_replace('__', '/', $subDir . '/');
        $dir = $this->path . $tmp;
        $res = scandir($dir);


        $curSubDir = $subDir.'/';
        if (!empty($origSubDir)) {
            $curSubDir = $tmp;
        }

        $skip = array('.', '..');

        $result = array();
        foreach ($res as $fname) {
            if (in_array($fname, $skip)) {
                continue;
            }
            if (is_file($dir . '/' . $fname)) {
                $result[] = $curSubDir . $fname;
            } elseif ($recurcive) {

                $result = array_merge($result,
                                      $this->getFileList($tmp . $fname,
                                                         $recurcive, $subDir));
            }
        }

        return $result;
    }

    /*
     * creates a file && all needed directories
     * 
     * @param string $path - global path for file to create
     * @param string $data - the text to write to the file
     * 
     * returns #of bytes written to file
     */
    public function createFile($path, $data = '')
    {
        if (file_exists($path)) {
            return;
        }
        
        $this->replaceFile($path, $data);
    }
    
    /*
     * replases a contents of a file && creates all needed directories for file to exist
     * 
     * @param string $path - global path for file to create
     * @param string $data - the text to write to the file
     * 
     * returns #of bytes written to file
     */
    public function replaceFile($path, $data = '')
    {
        $this->ensureDir($path, true, 0777);
        
        $ret = file_put_contents($path, $data);

        if (!$ret) {
            if (strlen($data) === 0) {
                return true;
            }
            return false;
        }
        return $ret;
    }
    
    
    
    /*
     * recursive remove a directory 
     * 
     * @param string $dir - the directory to remove
     */
	public function rrmdir($dir) 
	{
	   if (is_dir($dir)) 
	   {
		 $objects = scandir($dir);
		 foreach ($objects as $object) 
		 {
		   if ($object != "." && $object != "..") 
		   {
			 if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
		   }
		 }
		 reset($objects);
		 rmdir($dir);
	   }
	}

}
