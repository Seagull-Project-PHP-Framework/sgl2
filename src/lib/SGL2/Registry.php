<?php


class SGL2_Registry extends SGL2_Context
{
    protected
            $dispatcher          = null,
            $config			     = null,
            $components          = array();

    protected static
            $instances = array(),
            $current   = 'default';

    public static function createInstance(Zend_Config $config, $name = null, $class = __CLASS__)
    {
        if (null === $name) {
            $name = $config->app->name;
        }
        self::$current = $name;
        self::$instances[$name] = new $class();

        if (!self::$instances[$name] instanceof SGL2_Context) {
            throw new Exception(sprintf('Class "%s" is not of the type SGL2_Context.', $class));
        }
        self::$instances[$name]->init($config);
        return self::$instances[$name];
    }

    public function init(Zend_Config $config)
    {
        $this->config = $config;
        $this->dispatcher = Uber_Event_Dispatcher::getInstance();
    }

    public static function getInstance($name = null, $class = __CLASS__)
    {
        if (null === $name) {
            $name = self::$current;
        }

        if (!isset(self::$instances[$name])) {
            throw new Exception(sprintf('The "%s" context does not exist.', $name));
        }
        return self::$instances[$name];
    }

    public static function hasInstance($name = null)
    {
        if (null === $name) {
            $name = self::$current;
        }
        return isset(self::$instances[$name]);
    }

    public function getConfig()
    {
        return $this->config;
    }


    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    public function getLogger()
    {
		return isset($this->components['logger']) ? $this->components['logger'] : null;
    }

    public function getDbConnection($name = 'default')
    {
        if (null !== $this->components['databaseManager']) {
            return $this->components['databaseManager']->getDatabase($name)->getConnection();
        }
        return null;
    }

    public function getDbManager()
    {
        return isset($this->components['databaseManager']) ? $this->components['databaseManager'] : null;
    }

    public function getRequest()
    {
        return isset($this->components['request']) ? $this->components['request'] : null;
    }

    public function getResponse()
    {
        return isset($this->components['response']) ? $this->components['response'] : null;
    }

    public function setResponse($response)
    {
        $this->components['response'] = $response;
    }

    public function getRouter()
    {
        return isset($this->components['router']) ? $this->components['router'] : null;
    }

    public function getUser()
    {
        return isset($this->components['user']) ? $this->components['user'] : null;
    }


    /**
     * Returns true if the context object exists (implements the ArrayAccess interface).
     *
     * @param  string $name The name of the context object
     * @return Boolean true if the context object exists, false otherwise
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * Returns the context object associated with the name (implements the ArrayAccess interface).
     *
     * @param  string $name  The offset of the value to get
     * @return mixed The context object if exists, null otherwise
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Sets the context object associated with the offset (implements the ArrayAccess interface).
     *
     * @param string $offset The parameter name
     * @param string $value The parameter value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unsets the context object associated with the offset (implements the ArrayAccess interface).
     *
     * @param string $offset The parameter name
     */
    public function offsetUnset($offset)
    {
        unset($this->components[$offset]);
    }

    /**
     * Gets an object from the current context.
     *
     * @param  string $name  The name of the object to retrieve
     * @return object The object associated with the given name
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new Exception(sprintf('The "%s" object does not exist in the current context.', $name));
        }
        return $this->components[$name];
    }

    /**
     * Puts an object in the current context.
     *
     * @param string $name    The name of the object to store
     * @param object $object  The object to store
     */
    public function set($name, $object)
    {
        $this->components[$name] = $object;
    }

    /**
     * Returns true if an object is currently stored in the current context with the given name, false otherwise.
     *
     * @param  string $name  The object name
     * @return bool true if the object is not null, false otherwise
     */
    public function has($name)
    {
        return isset($this->components[$name]);
    }
}
