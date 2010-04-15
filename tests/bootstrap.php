<?php
//  sgl libs
$sglLibDir = dirname(__FILE__) . '/../src/lib/';

require $sglLibDir . 'Uber.php';
require_once 'VariousTest.php';

Uber::init();

Uber_Loader::registerNamespace('Uber', $sglLibDir);
Uber_Loader::registerNamespace('SGL2', $sglLibDir);
Uber_Loader::registerNamespace('HTML', $sglLibDir);
Uber_Loader::registerNamespace('Zend', $sglLibDir);
Uber_Loader::registerNamespace('Horde', $sglLibDir);

?>