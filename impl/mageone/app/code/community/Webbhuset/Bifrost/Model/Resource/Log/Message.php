<?php

class Webbhuset_Bifrost_Model_Resource_Log_Message
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('whbifrost/log_message', 'message_id');
    }
}
