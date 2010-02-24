<?php

require '../lib/SGL2.php';
require 'Uber.php';
Uber::init();

//  sgl libs + cms libs
$sglPath = dirname(dirname(__FILE__));
$sglLibDir = $sglPath .'/lib';
Uber_Loader::registerNamespace('SGL2', $sglLibDir);

$erh = SGL2_ErrorHandler::singleton();
$ech = SGL2_ExceptionHandler::singleton();

try {
    $front = new SGL2_Controller_Front();
    $front->run();

} catch (Exception $e) {
    print '<pre>'; print_r($e);
}

?>