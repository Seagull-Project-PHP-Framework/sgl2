<?php

class SGL2_Response
{
    /**
     * Response data
     *
     * @var array
     */
    protected $_aProps;

    /**
     * HTTP status code
     *
     * @var integer
     */
    protected $_code;

    /**
     * Stores output string to be returned to user
     *
     * @var string
     */
    protected $_data;

    /**
     * List of messages to be returned to user
     *
     * @var array
     */
    protected $_aMessages;

    /**
     * HTTP headers
     *
     * @var array
     */
    protected $aHeaders = array();

    public function set($k, $v)
    {
        $this->_aProps[$k] = $v;
    }

    public function add(array $aData)
    {
        foreach ($aData as $k => $v) {
            $this->_aProps[$k] = $v;
        }
    }

    public function setMessages(array $aMessages)
    {
        $this->_aMessages = $aMessages;
    }


    /**
     * If object attribute does not exist, magically set it to data array
     *
     * @param unknown_type $k
     * @param unknown_type $v
     */
    public function __set($k, $v)
    {
        if (!isset($this->$k)) {
            $this->_aProps[$k] = $v;
        }
    }

    public function __get($k)
    {
        if (isset($this->_aProps[$k])) {
            return $this->_aProps[$k];
        }
    }

    public function getHeaders()
    {
        return $this->aHeaders;
    }

    public function getBody()
    {
        return $this->_aProps;
    }

    public function setBody($body)
    {
        $this->_data = $body;
    }

    public function addHeader($header)
    {
        if (!in_array($header, $this->aHeaders)) {
            $this->aHeaders[] = $header;
        }
    }

    public function setCode($code)
    {
        $this->_code = $code;
    }

    public function __toString()
    {
        return $this->_data;
    }

    /**
     * Used for outputting template in layout
     *
     * @param string $templateEngine
     */
    public function outputBody($templateEngine = null)
    {
        if (!$this->template) {
            return;
        }
        $this->layout = $this->template;

#FIXME
        //  considerable hack to workaround recursive Flexy call
        $aData = $this->getBody();
        unset($aData['x'], $aData['_t'], $aData['this']);
        $resp = (object) $aData;

        $view = new SGL2_View_Html($resp, $templateEngine);
        echo $view->render();
    }
}
?>