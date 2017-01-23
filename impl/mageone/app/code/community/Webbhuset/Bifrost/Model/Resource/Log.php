<?php
/**
 * Log resource model
 *
 * @package Webbhuset
 * @module  Webbhuset_Bifrost
 * @author  Webbhuset <info@webbhuset.se>
 */
class Webbhuset_Bifrost_Model_Resource_Log
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('whbifrost/log', 'log_id');
    }

    /**
     * Delete log messages
     *
     * @param array $data
     *
     * @access public
     */
    public function deleteLogMessage($logId)
    {
        if (empty($logId)) {
            return;
        }
        $adapter = $this->_getWriteAdapter();
        $table   = $this->getTable('whbifrost/log_message');
        $where   = ['log_id = ?' => $logId];
        $adapter->delete($table, $where);
    }

    /**
     * Insert log messages
     *
     * @param array $data
     *
     * @access public
     */
    public function insertLogMessage($data)
    {
        if (empty($data)) {
            return;
        }
        $adapter = $this->_getWriteAdapter();
        $table   = $this->getTable('whbifrost/log_message');
        $adapter->insertMultiple($table, $data);
    }
}
