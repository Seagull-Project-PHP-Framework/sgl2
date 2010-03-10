<?php

//require 'Uber.php';
//Uber::init();
//
////  sgl libs
//$root = __DIR__;
//$sglLibDir = $root .'/sgl2/src/lib';
//Uber_Loader::registerNamespace('SGL2', $sglLibDir);

/*

to run test suite, cd to this dir and:
$ phpunit AllTests.php

*/

//require_once dirname(__FILE__) . '/../../SGL2.php';
require_once 'VariousTest.php';

class SGL2_AllTests {

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->setName('SGL2');
        $suite->addTestSuite('VariousTest');
        return $suite;
    }
}

if (PHP_SAPI != 'cli') {
    SGL2_AllTests::main();
}

?>