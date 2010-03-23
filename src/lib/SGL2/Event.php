<?php
class SGL2_Event extends Uber_Event
{
    //	custom hooks
    const HOOK_FIRST = 1;
    const HOOK_SECOND = 2;

    function __construct($oSubject, $eventName, array $params = array())
    {
        parent::__construct($oSubject, $eventName, $params);
    }
}
?>