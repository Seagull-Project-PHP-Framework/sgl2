<?php

abstract class SGL2_View_Abstract
{
    /**
     * Response object.
     *
     * @var SGL2_Response
     */
    public $data;

    /**
     * Reference to renderer strategy.
     *
     * @var SGL2_View_Renderer_Abstract
     */
    protected $_rendererStrategy;

    /**
     * Constructor.
     *
     * @param SGL2_Response $response
     * @param SGL2_View_Renderer_Abstract $rendererStrategy
     */
    public function __construct($response, SGL2_View_Renderer_Abstract $rendererStrategy)
    {
        $this->data = $response;
        $this->_rendererStrategy = $rendererStrategy;
    }

    /**
     * Delegates rendering strategy based on view.
     *
     * @return string   Rendered output data
     */
    public function render()
    {
        return $this->_rendererStrategy->render($this);
    }
}

?>