<?php

/**
 * Test suite.
 *
 * @package SGL
 * @author  Demian Turner <demian@phpkitchen.com>
 */
class VariousTest extends PHPUnit_Framework_TestCase
{
    
	function setup()
	{

	}
	
    public function testFoo()
    {
		$ctx = new SGL2_Context();
		$this->assertTrue(is_a($ctx, 'SGL2_Context'));
    }

	/**
	 * @expectedException Exception
	 */
    public function testNonExistentProperty()
    {
		$ctx = new SGL2_Context(array('zero', 'one', 'example'=>'e.g.'));
		echo $ctx->quux;
    }

    public function testProperty()
    {
		$ctx = new SGL2_Context(array('zero', 'one', 'example'=>'e.g.'));
		$ctx->foo = 'foo';
    }

    public function testAppContext()
    {
        $configFile = TEST_PATH . '/config.ini';
		$config = new Zend_Config_INI($configFile, 'staging');
        $registry = SGL2_Registry::createInstance($config);

		$registry2 = SGL2_Registry::getInstance();
		$this->assertThat(
			$registry,
			$this->identicalTo($registry2)
		);
		$this->assertTrue(SGL2_Registry::hasInstance());
		$registry->set('foo', new Foo());
		$this->assertSame($registry->get('foo'), $registry2->get('foo'));
		$this->assertTrue(is_a($registry->get('foo'), 'Foo'));		
    }

   
}

class Foo
{
	
}

?>
