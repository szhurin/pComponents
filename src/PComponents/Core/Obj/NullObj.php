<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * NullObj - a Dummy object to be able to be put into template engin or 
 *
 * @author sergey
 */
namespace PComponents\Core\Obj;

class NullObj
{
    private $call;
    private $get;
    
    
    public function __construct( array $defaults = array('call'=>'', 'get'=>false))
    {
        $this->call = $defaults['call'];
        $this->get = $defaults['get'];
    }
    
    public function __call($name, $arguments)
    {
        return $this->call;
    }
    
    public function __get($name)
    {
        return $this->get;
    }
}