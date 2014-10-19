<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Core\Test;

/**
 * Description of Base
 *
 * @author sergey
 */
class Base
{

    static public $verbouse         = 1;
    static public $quiet            = 0;
    static public $errorsOnly       = 2;
    static public $errorsStackTrace = 3;
    static public $functionInit     = 3;
    static public $subClassProperty = 4;
    private $stackDepth             = 2;
    private $errorCount             = 0;
    private $blockMessage           = '';

    public function __construct($stackDepth = null)
    {

        $this->stackDepth = ($stackDepth) ? $stackDepth : self::$functionInit;
    }

    /**
     *   DESC
     *
     *   @param type @name - param desc
     *
     *   @return type - desc of return
     */
    public function startBlock($blockMesage)
    {
        $this->errorCount   = 0;
        $this->blockMessage = $blockMesage;
    }

    public function endBlock($onErrorsOnly = true, $blockMesage = false)
    {
        $am = $this->errorCount;

        if ($am > 0) {
            echo '<br> ' . $this->blockMessage . ' has ' . $am . ' errors ! <br>';
        } elseif (!$onErrorsOnly) {
            echo '<br>  OK in ' . $this->blockMessage . ' <br>';
        }

        if ($blockMesage !== false) {
            $this->startBlock($blockMesage);
        }
        echo '<hr>';
    }

    public function assumeEmpty($obj, $verbouse = 2, $errorMessage = '')
    {
        $this->stackDepth++;
        $ret = $this->assumeEquals(true, empty($obj), $verbouse, $errorMessage);
        $this->stackDepth--;
        return $ret;
    }

    public function assumeNotEmpty($obj, $verbouse = 2, $errorMessage = '')
    {
        $this->stackDepth++;
        $ret = $this->assumeEquals(false, empty($obj), $verbouse, $errorMessage);
        $this->stackDepth--;
        return $ret;
    }

    public function assumeClsName($name, $obj, $verbouse = 2, $errorMessage = '')
    {
        $this->stackDepth++;
        $ret = $this->assumeEquals($name, get_class($obj), $verbouse,
                                                    $errorMessage);
        $this->stackDepth--;
        return $ret;
    }

    public function assumeArraysEquals($one, $two, $verbouse = 2,
                                       $errorMessage = '')
    {
        return $this->assumeCompare(array($this, 'arraysEqual'), $one, $two, $verbouse, $errorMessage);
    }

    public function assumeStrictEquals($one, $two, $verbouse = 2,
                                       $errorMessage = '')
    {
        return $this->assumeCompare('===', $one, $two, $verbouse, $errorMessage);
    }

    public function assumeEquals($one, $two, $verbouse = 2, $errorMessage = '')
    {
        return $this->assumeCompare('==', $one, $two, $verbouse, $errorMessage);
    }

    private function assumeCompare($type, $one, $two, $verbouse = 2,
                                   $errorMessage = '')
    {
        $message = $errorMessage;
        $data    = $this->checkAssume($type, $one, $two, $message);
        $called  = $data['data'];
        if ($data['status'] == 0) {  // assamption correct
            $mess = 'OK at line ' . $called['line'] . ' of ' . $called['file'] . ' ' . $message . ' <br>' . PHP_EOL;
            if ($verbouse == self::$verbouse) {
                echo $mess;
                return array('status' => 0, 'message' => $mess, 'data' => $called);
            } elseif ($verbouse === self::$errorsOnly) {
                return array('status' => 0, 'message' => $mess, 'data' => $called);
            } elseif ($verbouse === self::$quiet) {
                return array('status' => 0, 'message' => $mess, 'data' => $called);
            }

            return array('status' => 0, 'message' => $mess, 'data' => $called);
        } else {// assamption Incorrect
            $this->errorCount++;
            $mess = var_export($one, true) .
                    ' not ' . $type . ' to ' .
                    var_export($two, true) . ' ' .
                    '  on line ' . $called['line'] .
                    ' in file ' . $called['file'];
            $mess .= '   -----  ' . $message . ' <br>' . PHP_EOL;
            if ($verbouse == self::$verbouse) {
                echo $mess;
                return array('status' => 1, 'message' => $mess, 'data' => $called);
            } elseif ($verbouse === self::$errorsOnly) {
                echo $mess;
                return array('status' => 1, 'message' => $mess, 'data' => $called);
            } elseif ($verbouse === self::$errorsStackTrace) {
                $tmp = $this->getStackTrace($called['trace'], $this->stackDepth);
                //$tmp = $this->getStackTrace(null, 0);
                echo $mess . $tmp;
                return array('status' => 1, 'message' => $mess, 'data' => $called);
            } elseif ($verbouse === self::$quiet) {
                return array('status' => 1, 'message' => $mess, 'data' => $called);
            }

            return array('status' => 1, 'message' => $mess, 'data' => $called);
        }
    }

    protected function checkAssume($type = 'equals', $one = '1', $two = '1',
                                   $message = '')
    {

        $called = $this->getCalledInfo($this->stackDepth);

        if (is_callable($one)) {
            $realOne = $one();
        } else {
            $realOne = $one;
        }

        if (is_callable($two)) {
            $realTwo = $two();
        } else {
            $realTwo = $two;
        }


        if (is_callable($type)) {
            $tmp = $type($one, $two);
            if ($tmp) {
                return array('status' => 0, 'message' => '', 'data' => $called);
            } else {
                return array('status' => 1, 'message' => $message, 'data' => $called);
            }
        } else {
            switch ($type) {
                case 'equals':
                case '=':
                case '==':

                    if ($realOne == $realTwo) {
                        return array('status' => 0, 'message' => '', 'data' => $called);
                    } else {
                        return array('status' => 1, 'message' => $message, 'data' => $called);
                    }

                    break;
                case '===':
                    if ($realOne === $realTwo) {
                        return array('status' => 0, 'message' => '', 'data' => $called);
                    } else {
                        return array('status' => 1, 'message' => $message, 'data' => $called);
                    }

                    break;

                default:
                    break;
            }
        }


        return array('status' => -1, 'message' => 'Incorrect assumption TYPE', 'data' => $called);
    }

    protected function getCalledInfo($genBack = 1)
    {
        $e     = new \Exception();
        $trace = $e->getTrace();
        $line  = $trace[$genBack]['line'];
        $file  = $trace[$genBack]['file'];
        $func  = $trace[$genBack]['function'];
        return array('file' => $file, 'line' => $line, 'function' => $func, 'trace' => $trace);
    }

    protected function getStackTrace($trace = null, $genBack = 1)
    {
        if (empty($trace)) {
            $e     = new \Exception();
            $trace = $e->getTrace();
        }


        $tmp     = '<br>';
        $str_st  = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $str_end = '<br>';
        foreach ($trace as $k => $v) {
            if ($k < $genBack) {
                continue;
            }
            $tmp .= $str_st . "\t" . $v['line'] . "\t IN -> " . $v['file'] . '  FUNCTION ' . $v['function'] . $str_end;
        }


        return $tmp;
    }
    
    
    
    protected function arraysEqual( array $one, array $two)
    {
        if(count($one) !== count($two)){
            return false;            
        }        
        
        foreach($one as $key=>$val){
            if(!isset($two[$key])){
                return false;
            }
            $sec = $two[$key];
            if(is_array($val)){
                
                if(!is_array($sec)){
                    return false;
                }
                $res = $this->arraysEqual($val, $sec);
                if(!$res){
                    return FALSE;
                }
            }
            if($val != $sec){
                return false;
            }
        }
        return true;
        
    }

}
