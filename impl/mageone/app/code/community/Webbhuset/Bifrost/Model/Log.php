<?php
/**
 * Log model.
 *
 * @package Webbhuset
 * @module  Webbhuset_Bifrost
 * @author  Webbhuset <info@webbhuset.se>
 */
class Webbhuset_Bifrost_Model_Log
    extends Mage_Core_Model_Abstract
{
    /**
     * Number of rows to insert in each insert.
     *
     * @var int
     */
    protected $_batchSize = 1000;

    /**
     * Rows to insert.
     *
     * @var array
     */
    protected $_toInsert  = [];


    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('whbifrost/log');
    }

    /**
     * Finalize.
     *
     * @return void
     */
    public function finalize()
    {
        $this->_insertBatch();
        if (!$this->getCompletedAt()) {
            $this->setCompletedAt();
        }
    }

    /**
     * Write log message.
     *
     * @param string $message
     * @param string $type
     *
     * @return void
     */
    public function write($message, $type = Webbhuset_Bifrost_Model_System_Source_Log::TYPE_INFO)
    {
        $this->_toInsert[] = [
            'log_id'    => $this->getId(),
            'timestamp' => Mage::getModel('core/date')->gmtDate(),
            'type'      => $type,
            'message'   => $message,
        ];

        if (count($this->_toInsert) >= $this->_batchSize) {
            $this->_insertBatch();
        }
    }

    /**
     * Insert messages in database.
     *
     * @return void
     */
    protected function _insertBatch()
    {
        $resource = $this->getResource();
        $resource->insertLogMessage($this->_toInsert);
        $this->_toInsert = [];
    }

    /**
     * Get progress.
     *
     * @return integer
     */
    public function getProgress()
    {
        return $this->getUpdated()
            + $this->getSkipped()
            + $this->getCreated()
            + $this->getNotFound()
            + $this->getErrors();
    }

    /**
     * Sets completed at time.
     *
     * @param Mage_Core_Model_Date $time
     *
     * @return $this
     */
    public function setCompletedAt($time = null)
    {
        if (!$time) {
            $time = Mage::getModel('core/date')->gmtDate();
        }
        $this->setData('completed_at', $time)->save();

        return $this;
    }
}
