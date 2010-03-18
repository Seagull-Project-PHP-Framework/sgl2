<?php

class SGL2_Router_Exception extends Exception
{
	public function __construct($message, $param1 = false, $param2 = false)
	{
		if (is_array($param1)) {
			$i = 1;
            foreach ($param1 as $key => $value) {
                if (!empty($value) && !is_scalar($value)) {
                    continue;
                }
                $value 	 = str_replace('%', '%%', $value);
                $message = str_replace("%$i%", $value, $message);
                $message = str_replace("%$i", $value, $message);
                $message = str_replace("%$key%", $value, $message);
                $message = str_replace("%$key", $value, $message);
                $i++;
            }			
			parent::__construct(vsprintf($message, $param1));
		} else {
			parent::__construct($message);
		}
	}	
};

?>