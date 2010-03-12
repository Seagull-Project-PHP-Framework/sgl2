<?php

class SGL2_Controller_Front
{
	public function bootstrap()
	{
		$b = new SGL2_Bootstrap();
		$methods = get_class_methods($b);
		foreach ($methods as $method) {
			$b->$method();
		}
		return $this;
	}

    public function dispatch()
    {
		$ctx = SGL2_Context_App::getInstance();
		$request = $ctx->getRequest();
		$response = $ctx->getResponse();
		$ret = false;
		try {
			$ret = $this->processRequest($ctx, $request, $response);
		} catch (Exception $e) {
			throw $e;
		}
		return $ret;
	}

	public function processRequest(SGL2_Context $appCtx, SGL2_Request $request,
		SGL2_Response $response)
	{
		$appController = new SGL2_Controller_Application($appCtx);
		$appController->handleRequest($request, $response);
		$ret = $appController->handleResponse($request, $response);
		return $ret;
	}
}
?>