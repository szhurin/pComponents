<?php

include_once realpath(__DIR__.'/..').'/vendor/autoload.php';

$struc = new \PComponents\Cli\BasicStructure();


$path = getcwd() . '/';
$pathDir = new \PComponents\Cli\Settings();
$settings = $pathDir->findFSSettings($path);

$mode = 0775;

$params = array('--ns' => '', '--name' => '', '-y' => false);

foreach ($argv as $k => $v) {

    if (isset($params[$v])) {
        
        if ($params[$v] === false) {
            $params[$v] = true;
        } elseif (isset($argv[$k + 1])) {
            $params[$v] = $argv[$k + 1];
        }
    }
}

if (empty($params['--ns'])) {
    $params['--ns'] = $settings['ns'];
}

$cur_dir = \PComponents\Tools\Path::getCurrentDirName($path);

$needToCreateDir = false;

if (!empty($params['--name']) && $params['--name'] !== $cur_dir) {
    $cur_dir = $params['--name'];
    $needToCreateDir = true;
    if($settings['__path'] !== false){
        $params['--ns'] .= str_replace('/', '\\', $settings['__ns_path']) .'\\'.$cur_dir;
    }else{
        $params['--ns'] .= '\\'.$cur_dir;
    }
    $path .= $cur_dir . '/';
}

//var_dump($settings);



if(substr($params['--ns'],0,1) == '\\'){
    $params['--ns'] = substr($params['--ns'], 1);
}

$replace['{{namespace}}'] = $params['--ns'];
$replace['{{componentName}}'] = $cur_dir;



if ($params['-y'] === false) {


    echo "Do you want to create ".PHP_EOL.
            "Conponent with name '" . $cur_dir ."'".PHP_EOL.
            " in '" . $path . "'".PHP_EOL.
            " with namespace '" . $params['--ns'] ."'".PHP_EOL.
            " Type 'yes'/'y' to continue: ";

    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) != 'y' && trim($line) != 'yes') {
        echo "ABORTING!\n";
        exit;
    }
    echo "\n";
    echo "Thank you, continuing...\n";
}


 if ($needToCreateDir && !file_exists($cur_dir)) {
        mkdir($cur_dir);
        chmod($cur_dir, 0775);
    }

$creator = new \PComponents\Cli\ComponentCreator($path, $mode);

$ret = $creator->processStruc($struc->struc,   $replace);

echo PHP_EOL . 'Component succesfully created!' . PHP_EOL . PHP_EOL;
