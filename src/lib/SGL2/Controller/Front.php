<?php

class SGL2_Controller_Front
{
    protected
        $registry	  	= null,
        $dispatcher   = null;

    /**
     * @return $this
     */
    public function bootstrap()
	{
		$b = new SGL2_Bootstrap();						
		$methods = get_class_methods($b);
		foreach ($methods as $method) {
			$b->$method();
		}
		$this->registry	= SGL2_Registry::getInstance();
        $this->dispatcher = $this->registry->getEventDispatcher();

        $this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.afterBootstrap'));
		return $this;
	}

    /**
     * @return string
     * @throws Exception
     */
    public function dispatch()
    {
		$request 	= $this->registry->getRequest();
		$response 	= $this->registry->getResponse();
		$router 	= $this->registry->getRouter();

		$ret = false;
		try {
            $this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.beforeRouting'));
            // returns false if called from CLI, not tested
			$ok = $router->route($request->getUri());
            $this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.afterRouting'));
			$ret = $this->processRequest($request, $response);
		} catch (Exception $e) {
			throw $e;
		}
		return $ret;
	}

    /**
     * @param SGL2_Request $request
     * @param SGL2_Response $response
     * @return string
     */
    public function processRequest(SGL2_Request $request, SGL2_Response $response)
	{
        $this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.beforeDispatchLoop'));
		$appController = new SGL2_Controller_Application($this->registry);
		$appController->handleRequest($request, $response);
		$ret = $appController->handleResponse($response);
        $this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.afterDispatchLoop'));
		return $ret;
	}		
}
?>