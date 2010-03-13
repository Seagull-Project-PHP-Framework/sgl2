<?php

abstract class SGL2_Command_Abstract
{
	abstract public function __construct(SGL2_Registry $registry);
		
	abstract public function validate(SGL2_Request $request);
	
	abstract public function execute(SGL2_Request $request, SGL2_Response $response);
}

?>