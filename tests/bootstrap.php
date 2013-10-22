<?php

$root = dirname(dirname(dirname(__FILE__)));
define('PROJECT_PATH', $root . '/sgl2_demo_app');
define('TEST_PATH', $root . '/sgl2/tests');

//  setup paths
$sglLibDir = $root .'/sgl2/src/lib';
$sglModuleDir = PROJECT_PATH . '/modules';

// set include_path
set_include_path(get_include_path() . PATH_SEPARATOR . $sglLibDir . PATH_SEPARATOR . $sglModuleDir);

function autoload($className)
{
    $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require $fileName;
}

spl_autoload_register('autoload');

?>