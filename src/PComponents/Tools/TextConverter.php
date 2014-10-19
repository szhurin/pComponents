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

    static public function replaceArray()
    {
        if (empty(self::$lowDashArray)) {
            self::$lowDashArray = array_map(function($ch) {
                return '_' . $ch;
            }, range('a', 'z'));
        }
        return self::$lowDashArray;
    }

    /*
     *  
     */

    static public function lodashToCamel($str)
    {

        $lookfor = self::replaceArray();
        return str_replace($lookfor, range('A', 'Z'), $str);
    }

    static public function camelToLodash($str, $skipfirst = true)
    {
        if ($skipfirst) {
            $str = lcfirst($str);
        }
        $replaceTo = self::replaceArray();
        return str_replace(range('A', 'Z'), $replaceTo, $str);
    }

}
