<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Event
 *
 * @author sergey
 */

namespace PComponents\Tools;

class TextConverter
{

    static $lowDashArray = null;
    
    
    static public function replaceArray( $dash_str = '_')
    {
        if (empty(self::$lowDashArray)) {
            self::$lowDashArray = array_map(function($ch) use($dash_str) {
                return $dash_str . $ch;
            }, range('a', 'z'));
        }
        return self::$lowDashArray;
    }

    /*
     *  
     */

    static public function lodashToCamel($str, $dash_str = '_')
    {

        $lookfor = self::replaceArray($dash_str);
        return str_replace($lookfor, range('A', 'Z'), $str);
    }

    static public function camelToLodash($str, $skipfirst = true, $dash_str = '_')
    {
        if ($skipfirst) {
            $str = lcfirst($str);
        }
        $replaceTo = self::replaceArray($dash_str);
        return str_replace(range('A', 'Z'), $replaceTo, $str);
    }

}
