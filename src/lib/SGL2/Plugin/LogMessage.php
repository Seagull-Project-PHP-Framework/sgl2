<?php

class SGL2_Plugin_LogMessage extends Uber_Plugin_Abstract
{
    public function handleEvent(Uber_Event $e, $data = null)
    {
		$registry = SGL2_Registry::getInstance();	
		$logger = $registry->getLogger();
		$msg = 'Event '.$e->getName();
		$logger->log($msg, Zend_Log::INFO);
		
        // if (! is_null($data)) {
        //     $txt = $data->get();
        //     $newData = strrev($txt);
        //     $data->set($newData);
        // }
    }	
}

?>