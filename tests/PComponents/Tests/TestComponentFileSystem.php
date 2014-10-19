<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Tests;

/**
 * Description of TestComponentFileSystem
 *
 * @author sergey
 */
class TestComponentFileSystem
{

    public function __construct()
    {
        $this->test = new \PComponents\Core\Test\Base();
    }

    public function testGetFileList()
    {
        $path = __DIR__ . '/__dir_for_test';

        $fs = new \PComponents\Tools\FileSystem($path);

        $files = array(
            'test/test.text',
            'test/model/test.text',
            'test/tmp/tmp.text',
            'test/t.txt',
        );

        foreach ($files as $file) {
            $fs->createFile($path . '/' . $file);
        }

        /* @var $this->test  \PComponents\Core\Test\Base */
        $retArray = $fs->getFileList('test');
        sort($retArray);
        sort($files);
        $ret = $this->test->assumeArraysEquals($files, $retArray);
        
        if($ret['status'] != 0){
            var_dump($fs->getFileList('test'));
        }
        
        $fs->rrmdir($path .'/test');
    }

}
