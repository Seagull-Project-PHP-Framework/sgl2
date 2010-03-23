<?php

class SGL2_Controller_Front
{
	protected $registry;
	
	public function bootstrap()
	{
		$b = new SGL2_Bootstrap();						
		$methods = get_class_methods($b);
		foreach ($methods as $method) {
			$b->$method();
		}
		$this->registry	= SGL2_Registry::getInstance();		
		$this->registry->getEventDispatcher()->triggerEvent(new SGL2_Event($this, 'core.afterBootstrap'));		
		return $this;
	}

    public function dispatch()
    {
		$request 	= $this->registry->getRequest();
		$response 	= $this->registry->getResponse();
		$router 	= $this->registry->getRouter();

		$ret = false;
		try {
			$this->registry->getEventDispatcher()->triggerEvent(new SGL2_Event($this, 'core.beforeRouting'));					
			$aRet = $router->route($request->getUri());
			$this->registry->getEventDispatcher()->triggerEvent(new SGL2_Event($this, 'core.afterRouting'));								
			$ret = $this->processRequest($request, $response);
		} catch (Exception $e) {
			throw $e;
		}
		return $ret;
	}

	public function processRequest(SGL2_Request $request, SGL2_Response $response)
	{
		$this->registry->getEventDispatcher()->triggerEvent(new SGL2_Event($this, 'core.beforeDispatchLoop'));			
		$appController = new SGL2_Controller_Application($this->registry);
		$appController->handleRequest($request, $response);
		$ret = $appController->handleResponse($request, $response);
		$this->registry->getEventDispatcher()->triggerEvent(new SGL2_Event($this, 'core.afterDispatchLoop'));					
		return $ret;
	}		
}
?>