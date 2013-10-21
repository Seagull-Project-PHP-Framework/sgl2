<?php

//	BC for < PHP 5.3.x
if (!class_exists('SplQueue')) {
	require dirname(dirname(__FILE__)) .'/bc/splqueue/spldoublylinkedlist.php';
	require dirname(dirname(__FILE__)) .'/bc/splqueue/splqueue.php';	
}


/**
 * Class SGL2_Event_Dispatcher
 */
class SGL2_Event_Dispatcher
{
    protected $_listeners = array();
    protected $_globalListeners = null;
    private $_queue = null;
    private static $_instance = null;

    private function __construct()
    {
        $this->_queue = new SplQueue();
        //$this->_queue->setIteratorMode(SplQueue::IT_MODE_DELETE);
        $this->_globalListeners = new SGL2_Event_Listener_Collection();
    }

    /**
     * @return null|SGL2_Event_Dispatcher
     */
    public static function getInstance()
    {
        if (! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param $eventName
     * @param SGL2_Event_Listener_Interface $listener
     */
    public function addEventListener($eventName, SGL2_Event_Listener_Interface $listener)
    {
        if (! array_key_exists($eventName, $this->_listeners)) {
            $this->_listeners[$eventName] = new SGL2_Event_Listener_Collection();
        }
        $col = $this->_listeners[$eventName];
        $col->addListener($listener);
        // check the event queue
        foreach ($this->_queue as $event) {
            $this->_propagate($event, $data = null, false);
        }
    }

    /**
     * @param $eventName
     * @param SGL2_Event_Listener_Interface $listener
     * @return null
     */
    public function removeEventListener($eventName, SGL2_Event_Listener_Interface $listener)
    {
        if (! array_key_exists($eventName, $this->_listeners)) {
            return null;
        }
        $col = $this->_listeners[$eventName];
        $ok = $col->removeListener($listener);
    }

    /**
     * @param null $eventName
     * @return bool|int
     */
    public function getListenerCount($eventName = null)
    {
        if (is_null($eventName)) {
            //  count all listeners for all events
        }
        if (! isset($this->_listeners[$eventName])) {
            return false;
        }
        return count($this->_listeners[$eventName]);
    }

    /**
     * @param SGL2_Event_Listener_Interface $listener
     */
    public function addGlobalListener(SGL2_Event_Listener_Interface $listener)
    {
        $this->_globalListeners->addListener($listener);

        foreach ($this->_queue as $event) {
            $this->_propagate($event, $data = null, false);
        }
    }

    /**
     * @param SGL2_Event_Listener_Interface $listener
     * @return bool
     */
    public function removeGlobalListener(SGL2_Event_Listener_Interface $listener)
    {
        $col = $this->_globalListeners;
        $ok = $col->removeListener($listener);
        return $ok;
    }

    /**
     * @param SGL2_Event $e
     * @param null $data
     * @param bool $enQueue
     * @return SGL2_Event
     */
    public function triggerEvent(SGL2_Event $e, $data = null, $enQueue = false)
    {
        return $this->_propagate($e, $data, $enQueue);
    }

    /**
     * @param SGL2_Event $e
     * @param $data
     * @param $enQueue
     * @return SGL2_Event
     */
    protected function _propagate(SGL2_Event $e, $data, $enQueue)
    {
        if (array_key_exists($e->getName(), $this->_listeners)) {
            $col = $this->_listeners[$e->getName()];
            $col->propagate($e, $data);
        }
        if ($e->isCancelled()) {
            return $e;
        }
        $this->_globalListeners->propagate($e, $data);
        if ($e->isCancelled() || $enQueue == false) {
            return $e;
        }
        $this->_queue[] = $e;
        return $e;
    }

    /**
     * @param $eventName
     * @return SGL2_Event_Listener_Collection
     */
    public function getEventListeners($eventName)
    {
        if (array_key_exists($eventName, $this->_listeners)) {
            return $this->_listeners[$eventName];
        }
        return new SGL2_Event_Listener_Collection();
    }


    public function reset()
    {
        $this->_listeners = array();
        $this->_globalListeners = new SGL2_Event_Listener_Collection();
        $this->_queue = new SplQueue();
        $this->_queue->setIteratorMode(SplQueue::IT_MODE_DELETE);
    }
}
?>