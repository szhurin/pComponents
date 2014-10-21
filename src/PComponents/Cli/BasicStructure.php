<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Cli;

/**
 * Description of BasicStructure
 *
 * @author sergey
 */
class BasicStructure
{

    public $toReplace = array(
        'namespace'      => '',
        'componentsName' => ''
    );
    public $struc     = array(
        '--samples'                      => array(
            'type'     => 'dir',
            'contents' => array()
        ),
        'assets'                         => array(
            'type'     => 'dir',
            'contents' => array(
                'css'  => array(
                    'type'     => 'dir',
                    'contents' => array()
                ),
                'js'   => array(
                    'type'     => 'dir',
                    'contents' => array()
                ),
                'html' => array(
                    'type'     => 'dir',
                    'contents' => array()
                ),
            )
        ),
        'configs'                        => array(
            'type'     => 'dir',
            'contents' => array(
                '--samples.php' => array(
                    'type'     => 'file',
                    'contents' => '<?php return array("real sample file name"=>"requested file name");'
                ),
            )
        ),
        'controllers'                    => array(
            'type'     => 'dir',
            'contents' => array(
                'sampleController.php' => array(
                    'type'     => 'file',
                    'contents' => ''
                ),
            ),
        ),
        'models'                         => array(
            'type'     => 'dir',
            'contents' => array(
                'sampleModel.php' => array(
                    'type'     => 'file',
                    'contents' => ''
                ),
            ),
        ),
        'views'                          => array(
            'type'     => 'dir',
            'contents' => array()
        ),
        'tests'                          => array(
            'type'     => 'dir',
            'contents' => array()
        ),
        '{{componentName}}Component.php' => array(
            'type'     => 'file',
            'contents' => '',
        ),
        'Component.php'                  => array(
            'type'     => 'file',
            'contents' => '',
        ),
        'Element.php'                    => array(
            'type'     => 'file',
            'contents' => '',
        ),
    );
    private $contents = array(
        'Element.php'                    =>
        '<?php
 /**
 * Base Element file
 *
 * @author automatic generation tool
 */

namespace {{namespace}};

class Element extends \PComponents\Core\Element 
{

    //{{class_methods}}
}
',
        'Component.php'                  =>
        '<?php
 /**
 * Base Component file
 *
 * @author automatic generation tool
 */

namespace {{namespace}};

class Component extends \PComponents\Core\Component 
{

    //{{class_methods}}
}
',
        '{{componentName}}Component.php' =>
        '<?php
 /**
 * {{componentName}}Component - description
 *
 * @author automatic generation tool
 */

namespace {{namespace}};

class {{componentName}}Component extends Component 
{

    public function attach()  { }
    public function init()  { }
}
',
        'sampleModel.php'                =>
        '<?php
 /**
 * sample Model file
 *
 * @author automatic generation tool
 */

namespace {{namespace}}\controllers;
use {{namespace}};

class sampleModel extends {{componentName}}\Element
{

    //{{class_methods}}
}
',
        'sampleController.php'           =>
        '<?php
 /**
 * sample Controller file
 *
 * @author automatic generation tool
 */

namespace {{namespace}}\controllers;
use {{namespace}};

class sampleController extends {{componentName}}\Element
{

    //{{class_methods}}
}
'
    );

    
    public function __construct()
    {
        $this->processStruc($this->struc);
        
       // var_dump($this->struc);
    }
    
    
    
    
    private function processStruc(&$struc)
    {
        foreach($struc as $sname => $item ){
            $struc [$sname] = $this->processStrucItem($sname, $item);
        }
    }
    
    private function processStrucItem($name, $struc)
    {
        if(is_array($struc['contents'])){
            $this->processStruc($struc['contents']);
        }elseif(isset($this->contents[$name])){
            //var_dump($name);
            $struc['contents'] = $this->contents[$name];
            
        } 
        
        return $struc;
    }
}
