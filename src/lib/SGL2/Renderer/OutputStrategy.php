<?php

/**
 * Abstract renderer strategy
 *
 * @abstract
 * @package SGL
 */
abstract class SGL2_Renderer_OutputStrategy
{
    /**
     * Abstract render method.
     *
     * @param SGL2_View_Abstract $view
     */
    abstract public function render(SGL2_View_Abstract $view);
}


?>