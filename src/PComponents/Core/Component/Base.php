<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PComponents\Core\Component;

/**
 * Description of Build
 *
 * @author sergey
 */
class Base extends \PComponents\Core\Element
{
    private $structure = array(
        'configs',
        'controllers',
        'models',
        'tests',
        'views',
        'assets',
        '__component',
    );
    
    public function updateCacheExports()
    {
        /* @var $fs \PComponents\Tools\FileSystem   */
        $fs = $this->getFileSystem($this->path);
        
        var_dump([$this->cname, 'FS', $this->getExports()]);
        
        $fs->replaceFile(
                $this->path.'_pcd_exports.php', 
                '<?php '.PHP_EOL. ' return '.var_export($this->getExports(), true).';'
                );
    }
    
    public function getComponentName()
    {
        $name = get_class($this);
        return $name;
    }
}
