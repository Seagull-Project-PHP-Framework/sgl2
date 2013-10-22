<?php

class SGL2_Controller_Application
{
	protected
	  $registry	  	= null,
	  $dispatcher   = null;	
	
	public function __construct(SGL2_Registry $registry)
	{
		$this->registry = $registry;
		$this->dispatcher = $registry->getEventDispatcher();
	}	
	
	public function handleRequest(SGL2_Request $request, SGL2_Response $response)
	{
		$this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.beforeDispatch'));
		try {
			$controller = $this->resolveController($request);
			$cmd = $controller->resolveCommand($request); // while
			if ($cmd->validate($request)) {
				$cmd->execute($request, $response);			
			} else {
				//	handle validation fail
			}
		} catch (Exception $e) {
			throw $e;
		}
		$this->dispatcher->triggerEvent(new SGL2_Event($this, 'core.afterDispatch'));
	}
	
	public function handleResponse(SGL2_Response $response)
	{
		$view = $this->getView($response);
//		$template = $view->getTemplate($requestCtx, $responseCtx);
		$ret = $this->invokeView($view);
		return $ret;
//		$this->dispatch($appCtx, $requestCtx, $responseCtx);
	}

    /**
     * @param SGL2_Response $response
     * @return SGL2_View_Html
     */
    public function getView(SGL2_Response $response)
	{
		$config = $this->registry->getConfig();
		$response->layout = $config->modules->default->layout;
		$response->template = $config->modules->default->template;
		$response->theme = $config->site->defaultTheme;
//        $view = new SGL2_View_Text($response);		
        $view = new SGL2_View_Html($response);		
		return $view;
	}

	public function invokeView(SGL2_View_Abstract $view)
	{
		return $view->render();
	}		
	
	// public function dispatch(SGL2_Context $appCtx, SGL2_Request $request, SGL2_Response $response)
	// {
	// 	try {
	// 		$dispatcher = $appCtx->getRequestDispatcher($template);
	// 		$dispatcher->forward($request, $response);
	// 	} catch (Exception $e) {
	// 		throw $e;
	// 	}
	// }	
	
	public function resolveController(SGL2_Request $request)
	{
        $moduleName = ucfirst($request->getModuleName());
        $controllerName = $request->getControllerName();
        $controller = $this->loadController($moduleName, $controllerName);		
		return $controller;
	}		
	
	public function loadController($moduleName, $controllerName)
	{
		if (is_null($controllerName)) {
			//	we're only loading Commands, use this Controller
			$ret = $this;
			
		} else {
	        $class = ucfirst($moduleName) .'_Controller_'. ucfirst($controllerName);
	        $ret = new $class($this->registry);
		}
        return $ret;				
	}				
	
	//	commands should not know about views	
	public function resolveCommand(SGL2_Request $request)
	{
        $moduleName = ucfirst($request->getModuleName());
        $controllerName = ucfirst($request->getControllerName());
        $cmdName = ucfirst($request->getCmdName());
        $cmd = $this->loadCommand($moduleName, $controllerName, $cmdName);
        return $cmd;
	}
	
    public function loadCommand($moduleName, $controllerName, $cmdName)
    {
        $class = $moduleName.'_Command_'.$cmdName;
        $obj = new $class($this->registry);
        return $obj;
    }
}

?>