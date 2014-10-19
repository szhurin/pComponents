<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  BaseObj - base functions for ERROR system | Messaging System | namespace 
 *
 * @author sergey
 */
namespace PComponents\Core\Obj;

use PComponents\Tools as Tools;

class MixedObj
{
    
    private $mixins = array();

    public function addMixin( $mixin , $name = null)
    {
        if( $mixin instanceof IMixin){
            $mixin->setMixedObj( $this );
        }
        if(!empty($name)){
            $this->mixins[$name] = $mixin;
        }elseif (is_callable( array( $mixin, 'getName' ))){
                $this->mixins[$mixin->getName()] = $mixin;
            
        }else{
            $this->mixins[] = $mixin;
        }
        
    }

    public function hasMixin( $mixinName )
    {
        return array_key_exists( $mixinName, $this->mixins );
    }

    public function __call( $name, $arguments )
    {
        foreach ($this->mixins as $mixin) {
           if (is_callable( array( $mixin, $name ) )) {
               return call_user_func_array( array( $mixin, $name ), $arguments );
           }
        }

       throw new \Exception('Unknown method call.');
    }

}
