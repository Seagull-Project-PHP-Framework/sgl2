<?php

class SGL2_Request
{
    protected $data;

    public function __construct()
    {
        $this->data = new ArrayObject(array_merge($_GET, $_POST), 
			ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Retrieves values from Request object.
     *
     */
    public function get($key, $default = false)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return $default;
        }
    }

    /**
     * Set a key for Request object.
     *
     * @access  public
     * @param   mixed   $name   Request param name
     * @param   mixed   $value  Request param value
     * @return  void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }


    public function getModuleName()
    {
        if (isset( $this->data['moduleName'])) {
            $ret = $this->data['moduleName'];
        } else {
            $ret = 'default';
        }
        return $ret;
    }

    public function getControllerName()
    {
        if (isset( $this->data['controllerName'])) {
            $ret = $this->data['controllerName'];
        } else {
            $ret = null;
        }
        return $ret;
    }

    public function getCmdName()
    {
        if ( isset($this->data['cmd'])) {
            $ret = $this->data['cmd'];
        } else {
            $ret = 'default';
        }
        return $ret;
    }

	public function inCLI()
	{
		return 0 == strncasecmp(PHP_SAPI, 'cli', 3);
	}
	
	public function getUri()
	{
		if (!$this->inCLI()) {
			$ret = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$ret = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'Off')
				? 'http://' . $ret
				: 'https://' . $ret;			
		} else {
			$ret = false;
		}
		return $ret;
	}
}
?>
