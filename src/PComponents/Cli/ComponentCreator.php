<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Cli;

/**
 * Description of ComponentCreator
 *
 * @author sergey
 */
class ComponentCreator
{

    public $path; // = getcwd() . '/';
    public $mode;
    public $error = '';

    public function __construct($path, $mode = 0775)
    {
        $this->path = $path;
        $this->mode = $mode;
    }

    public function createNode($name, $node, $replace)
    {
        $old_path = $this->path;
        $this->error = '';
        if (file_exists($this->path . $name)) {
            $this->error .= $this->path . $name . ' node already exists';
            return false;
        }

        if ($node['type'] == 'dir') {
            $nodeName = $this->path . str_replace(
                                    array_keys($replace), 
                                    array_values($replace), 
                                    $name);
            
            if (!mkdir($nodeName, $this->mode, true)) {
                die('Не удалось создать директории... ' . $this->path . $name);
            }
            chmod($nodeName, $this->mode);
            if (!empty($node['contents'])) {
                $this->path = $nodeName . '/';
                $this->processStruc($node['contents'], $replace);
                $this->path = $old_path;
            }
            return true;
        } elseif ($node['type'] == 'file') {
            $nodeName = $this->path . str_replace(
                                    array_keys($replace), 
                                    array_values($replace), 
                                    $name);
            if (!file_put_contents(
                            $nodeName, 
                            str_replace(
                                    array_keys($replace), 
                                    array_values($replace), 
                                    $node['contents']))) {
                die('Не удалось создать file... ' . $this->path . $name);
            }
            chmod($nodeName, $this->mode);
            return true;
        }

        $this->error .= ' unknown node type ' . $node['type'];
        return false;
    }

    function processStruc($structure, $replace)
    {
        foreach ($structure as $name => $node) {
            if (!$this->createNode($name, $node, $replace)) {
                return false;
            }
        }
        return true;
    }

}
