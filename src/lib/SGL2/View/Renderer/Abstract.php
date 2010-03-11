<?php

/**
 * Abstract renderer strategy
 *
 * @abstract
 * @package SGL
 */
abstract class SGL2_View_Renderer_Abstract
{
    /**
     * Abstract render method.
     *
     * @param SGL2_View_Abstract $view
     */
    abstract public function render(SGL2_View_Abstract $view);
}


?>