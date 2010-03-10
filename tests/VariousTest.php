<?php

define('SGL2_PATH', dirname(__DIR__));

/**
 * Test suite.
 *
 * @package SGL
 * @author  Demian Turner <demian@phpkitchen.net>
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
		$config = new Zend_Config_Ini(SGL2_PATH.'/tests/config.ini', 'staging');
        $ctx = SGL2_Context_App::createInstance($config);
		$ctx2 = SGL2_Context_App::getInstance();
		$this->assertThat(
			$ctx,
			$this->identicalTo($ctx2)
		);
		$this->assertTrue(SGL2_Context_App::hasInstance());
		$ctx->set('foo', new Foo());
		$this->assertSame($ctx->get('foo'), $ctx2->get('foo'));
		$this->assertTrue(is_a($ctx->get('foo'), 'Foo'));		
    }

   
}

class Foo
{
	
}

?>
