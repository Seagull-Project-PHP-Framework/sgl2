<?php

class SGL2_Context extends ArrayObject
{
    /**
     * Constructs a parent ArrayObject with default
     * ARRAY_AS_PROPS to allow access as an object
     *
     * @param array $array data array
     * @param integer $flags ArrayObject flags
     */
    public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS)
    {
		parent::__construct($array, $flags);
    }

    /**
     * @param mixed $index
     * @return bool
     * @throws Exception
     *
     * Workaround for http://bugs.php.net/bug.php?id=40442 (ZF-960).
     */
    public function offsetExists($index)
	{
		if (!array_key_exists($index, $this)) {
			throw new Exception("No entry is registered for key '$index'");
		} else {
			return true;
		}
	}
	
	public function offsetGet($index)
	{
		if ($this->offsetExists($index)) {
			return $this->$index;
		}
	}

	/**
	 * Unsets the value associated with the offset (implements the ArrayAccess interface).
	 *
	 * @param string $offset The parameter name
	 */
	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}	
	
	  /*
	   * Sets the value of the offset (implements the ArrayAccess interface).
	   * disabled because segfaults on 5.3.0
	   *
	   * @param string $offset The parameter name
	   * @param string $value The parameter value
	   *
		public function offsetSet($offset, $value)
		{		
			$this->$offset = $value;
		}
	*/	
	
    public function get($index)
    {
        if (!$this->offsetExists($index)) {
            throw new Exception(sprintf('The "%s" object does not exist in the current context.', $index));
        }
        return $this->$index;
    }

    public function set($index, $value)
    {
        $this->$index = $value;
    }
}
?>