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
class TestComponentManager
{
    /**
     *
     * @var \PComponents\Core\Test\Base 
     */
    private $test;
    public function __construct()
    {
        $this->test = new \PComponents\Core\Test\Base();
    }

    public function testComponentDataCache()
    {
        $path = system_path . '/library/App/Components/';

        $man = new \PComponents\Core\Manager();

        $tmp = $man->getComponentObject($path .'Tests/sample');
        
        $this->test->assumeEquals($tmp instanceof \PComponents\Core\Component , true);
        
//        var_dump($man->updateComponentDirectory($path .'Test'));
        var_dump($man->updateComponentDirectory($path .'Tests'));
        var_dump($man->updateComponentDirectory($path .'Tests/TestComponent'));
        //var_dump($man->updateComponentDirectory($path .'Tests/sample'));
        
    }

}
