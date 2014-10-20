<?php

$struc = array(
    '--samples' => array(
        'type' => 'dir',
        'contents' => array()
    ),
    'assets' => array(
        'type' => 'dir',
        'contents' => array(
            'css' => array(
                'type' => 'dir',
                'contents' => array()
            ),
            'js' => array(
                'type' => 'dir',
                'contents' => array()
            ),
        )
    ),
    'configs' => array(
        'type' => 'dir',
        'contents' => array(
            '--samples.php' => array(
                'type' => 'file',
                'contents' => '<?php return array("real sample file name"=>"requested file name");'
            ),)
    ),
    'controllers' => array(
        'type' => 'dir',
        'contents' => array()
    ),
    'models' => array(
        'type' => 'dir',
        'contents' => array()
    ),
    'views' => array(
        'type' => 'dir',
        'contents' => array()
    ),
    'tests' => array(
        'type' => 'dir',
        'contents' => array()
    ),
    'SampleComponent.php' => array(
        'type' => 'file',
        'contents' => '<?php' . PHP_EOL
        . '/**
 * SampleComponent - description
 *
 * @author automatic generation tool
 */

namespace {{namespace}};

class {{componentName}}Component extends Component {

    public function attach()  { }
    public function init()  { }
}
'
    ),
    'Component.php' => array(
        'type' => 'file',
        'contents' => '<?php' . PHP_EOL
        . '/**
 * BaseComponent file
 *
 * @author automatic generation tool
 */

namespace {{namespace}};

class Component extends \PComponents\Core\Component {

    {{class_methods}}
}
'
    ),
    'Element.php' => array(
        'type' => 'file',
        'contents' => '<?php' . PHP_EOL
        . '/**
 * BaseElement file
 *
 * @author automatic generation tool
 */

namespace {{namespace}};

class Element extends \PComponents\Core\Element {

    {{class_methods}}
}
'
    ),
);
$mode = 0775;
$path = '/';
$error = '';

function createNode($name, $node)
{
    $old_path = $path;
    $error = '';
    if (file_exists($path . $name)) {
        $error .= ' node already exists';
        return false;
    }

    if ($node['type'] == 'dir') {
        if (!mkdir($path . $name, $mode, true)) {
            die('Не удалось создать директории... ' . $path . $name);
        }
        chmod($path . $name, $mode);
        if (!empty($node['contents'])) {
            $path += $name .'/';
            processStruc($node['contents']);
            $path = $old_path;
        }
        return true;
    } elseif ($node['type'] == 'file') {
        if (!file_put_contents($path . $name, $node['contents'])) {
            die('Не удалось создать file... ' . $path . $name);
        }
        chmod($path . $name, $mode);
        return true;
    }

    $error .= ' unknown node type ' . $node['type'];
    return false;
}

$path = getcwd() . '/';

function processStruc($structure)
{
    foreach ($structure as $name => $node) {
        if(!createNode($name, $node)){
            echo $error;
            exit;
        }
    }
}


processStruc($struc);