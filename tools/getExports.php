<?php
include_once realpath(__DIR__.'/..').'/vendor/autoload.php';

$path = getcwd() . '/';


$man = new \PComponents\Core\Manager();

        
//        var_dump($man->updateComponentDirectory($path .'Test'));
        var_dump($man->updateComponentDirectory($path));
        //var_dump($man->updateComponentDirectory($path .'Tests/TestComponent'));
        //var_dump($man->updateComponentDirectory($path .'Tests/sample'));

echo PHP_EOL . 'Component succesfully created!' . PHP_EOL . PHP_EOL;
