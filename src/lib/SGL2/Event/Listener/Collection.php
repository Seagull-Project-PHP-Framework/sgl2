<?php
class SGL2_Event_Listener_Collection implements Countable
{
    private $_listeners = array();

    public function addListener(SGL2_Event_Listener_Interface $listener)
    {
        array_push($this->_listeners, $listener);
        return count($this->_listeners);
    }

    public function propagate(SGL2_Event $e, $data = null)
    {
        foreach ($this->_listeners as $listener) {
            $listener->handleEvent($e, $data);
            if ($e->isCancelled()) {
                break;
            }
        }
    }

    public function removeListener(SGL2_Event_Listener_Interface $listener)
    {
        $key = array_search($listener, $this->_listeners);
        if ($key !== false) {
            unset($this->_listeners[$key]);
            $ret = true;
        } else {
            $ret = false;
        }
        return $ret;
    }

    public function count()
    {
        return count($this->_listeners);
    }
}
?>