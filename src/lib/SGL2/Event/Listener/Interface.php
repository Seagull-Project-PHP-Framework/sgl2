<?php
interface SGL2_Event_Listener_Interface
{

    public function handleEvent(SGL2_Event $e, $data = null);

    public function validate();
}
?>