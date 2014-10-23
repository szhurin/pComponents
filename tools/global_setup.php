<?php
$output = array();

exec('composer global update', $output);

$old_path = getcwd();

exec('cd ~/.composer/vendor/szhurin/PComponents', $output);
exec('composer install', $output);

exec('cd ~/.composer/vendor/bin', $output);

$cur_path = getcwd().'/';
var_dump($cur_path);
$createFName = $cur_path.'_pct_create.php';
if(!file_exists($createFName)){
    file_put_contents($createFName, "#!/usr/bin/php
<?php

include realpath(__DIR__.'/..').'/szhurin/PComponents/tools/createComponent.php';


");
}

$createFName = $cur_path.'_pct_build.php';
if(!file_exists($createFName)){
    file_put_contents($createFName, "#!/usr/bin/php
<?php

include realpath(__DIR__.'/..').'/szhurin/PComponents/tools/getExports.php';


");
}


