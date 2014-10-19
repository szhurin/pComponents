<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Manager is the tool to register an array of component objects  
 *
 * @author sergey
 */

namespace PComponents\Core;

class Test extends Element
{
    /* @var $this->test \Test\Base */
    protected $test;

    public function init()
    {
        $this->test = new Test\Base();
    }

}
