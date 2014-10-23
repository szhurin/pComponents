<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Core\Container;

/**
 * Description of DiContainer
 *
 * @author sergey
 */
class DiContainer extends Pimple
{
    
    
    /**
     * Sets manager for a container
     *
     * 
     * @param  PComponent\Manager  $manager    The manager instance
      */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }
    
    
    /**
     * Gets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or an object
     *
     * @throws InvalidArgumentException if the identifier is not defined
     */
    public function offsetGet($id)
    {
        
        
        if(!parent::offsetExists($id)){
            $man = $this->manager;
            $res = $man->registerService($id);// returns false if service not found 
            if(!$res){
                var_dump($man->getError());
            }
        }        
        return parent::offsetGet($id);
    }
            
            
}
