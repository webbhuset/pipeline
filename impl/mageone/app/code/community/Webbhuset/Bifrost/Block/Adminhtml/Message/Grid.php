<?php
/**
 * Log message grid.
 *
 * @copyright Copyright (C) 2016 Webbhuset AB
 * @author Webbhuset AB <info@webbhuset.se>
 */
class Webbhuset_Bifrost_Block_Adminhtml_Message_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * The log id to show messages for.
     *
     * @var int
     */
    protected $_logId;


    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('message_grid');
        $this->setSaveParametersInSession(false);
        $this->setEmptyText("Log is empty.");
        $this->setUseAjax(true);
        $this->setDefaultSort('message_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare Status Collection.
     *
     * @access protected
     * @return void
     */
    protected function _prepareCollection()
    {
        $collection   = Mage::getResourceModel('whbifrost/log_message_collection');
        $this->_logId = Mage::app()->getRequest()->getParam('id');
        $collection->addFieldToFilter('log_id', $this->_logId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Columns
     *
     * @access protected
     * @return void
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'message_id',
            [
                'header'    => 'ID',
                'width'     => '80px',
                'index'     => 'message_id',
                'type'      => 'number',
            ]
        );

        $this->addColumn(
            'timestamp',
            [
                'header'    => 'Timestamp',
                'width'     => '160px',
                'index'     => 'timestamp',
            ]
        );

        $this->addColumn(
            'type',
            [
                'header'    => 'Type',
                'width'     => '120px',
                'type'      => 'options',
                'options'   => Mage::getModel('whbifrost/log')->getTypeArray(),
                'index'     => 'type',
            ]
        );

        $this->addColumn(
            'message',
            [
                'header'    => 'Message',
                'index'     => 'message',
                'frame_callback'    => [$this, 'formatMessage'],
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Replaced newlines with html row breaks.
     *
     * @param string $value
     *
     * @return string
     */
    public function formatMessage($value)
    {
        return str_replace("\n", '<br>', $value);
    }

    /**
     * Returns grid url.
     *
     * @access public
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/messagegrid', ['id' =>  $this->_logId]);
    }

    /**
     * Returns row url
     *
     * @param Webbhuset_Peasant_Model_Job $row
     *
     * @access public
     * @return boolean
     */
    public function getRowUrl($row)
    {
        return false;
    }
}

