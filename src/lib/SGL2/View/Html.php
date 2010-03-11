<?php

/**
 * Wrapper for simple HTML views.
 *
 * @package SGL
 */
class SGL2_View_Html extends SGL2_View_Abstract
{
    /**
     * HTML renderer decorator
     *
     * @param SGL2_Response $data
     * @param string $templateEngine
     */
    public function __construct($response, $templateEngine = null)
    {
		$ctx = SGL2_Context_App::getInstance();
        //  prepare renderer class
        if (is_null($templateEngine)) {
            $templateEngine = $ctx->getConfig()->site->templateEngine;
        }
        $templateEngine =  ucfirst($templateEngine);
        $rendererClass  = 'SGL2_View_Renderer_Html_' . $templateEngine;

        parent::__construct($response, new $rendererClass);
    }
}
?>