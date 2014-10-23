<?php

/**
 * Manager is the tool to register an array of component objects
 * registering components services working with all registered components 
 *
 * @author sergey
 */

namespace PComponents\Core;

class Manager extends Manager\ComponentRegistration
{

    public $c                 = array();    // di container
    public function __construct($c = array())
    {
        $this->manager = new Obj\NullObj(); //no need for manager of the manager
        if (empty($c)) {
            $c = new Container\DiContainer();
        }
        $this->c = $c;

        $c->setManager($this);
        
        parent::__construct();
        }

}
