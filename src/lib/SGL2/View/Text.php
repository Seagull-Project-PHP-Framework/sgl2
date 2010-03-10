<?php

/**
 * Wrapper for simple HTML views.
 *
 * @package SGL
 */
class SGL2_View_Text extends SGL2_View_Abstract
{

    public function __construct(SGL2_Response $response, $templateEngine = null)
    {
        $rendererClass  = 'SGL2_Renderer_Text';

        parent::__construct($response, new $rendererClass);
    }
}
?>