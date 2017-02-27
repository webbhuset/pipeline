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
    const TYPE_INFO         = 'info';
    const TYPE_ERROR        = 'error';
    const TYPE_CREATED      = 'created';
    const TYPE_UPDATED      = 'updated';
    const TYPE_SKIPPED      = 'skipped';
    const TYPE_NOT_FOUND    = 'not_found';
    const TYPE_DELETED      = 'deleted';

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
    public function write($message, $type = self::TYPE_INFO)
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
        $resource->insertLogMessages($this->_toInsert);
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

    public function getTypeArray()
    {
        return [
            self::TYPE_INFO         => Mage::helper('whbifrost')->__('Information'),
            self::TYPE_CREATED      => Mage::helper('whbifrost')->__('Created'),
            self::TYPE_UPDATED      => Mage::helper('whbifrost')->__('Updated'),
            self::TYPE_SKIPPED      => Mage::helper('whbifrost')->__('Skipped'),
            self::TYPE_NOT_FOUND    => Mage::helper('whbifrost')->__('Not Found'),
            self::TYPE_DELETED      => Mage::helper('whbifrost')->__('Deleted'),
            self::TYPE_ERROR        => Mage::helper('whbifrost')->__('Error'),
        ];
    }
}
