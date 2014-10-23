<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Tools;

/**
 * Description of DataCollection
 *
 * @author sergey
 */
class DataCollection
{
    protected $entryArray = array();
    protected $entry = '';
    protected $entry_code = 0;
    
    
    public function unsetEntries()
    {
        $this->entryArray = array();
        $this->entry = '';
        $this->entry_code = 0;        
    }
    public function getEntry()
    {
        return array('code' => $this->entry_code, 'text' => $this->entry);
    }
    
    public function getEntries()
    {
        return $this->entry_code;
    }
    
    
    public function setEntry($text, $code = 1)
    {
        $this->entry = $text;
        $this->entry_code = $code;
        $this->entryArray[] = array('code' => $this->entry_code, 'text' => $this->entry); 
    }
    
    
    
}
