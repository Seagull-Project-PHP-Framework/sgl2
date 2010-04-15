<?php
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