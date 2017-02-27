<?php

/**
 * Adminhtml log grid.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class Webbhuset_Bifrost_Block_Adminhtml_Log_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('log_grid');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText("Log is empty.");
        $this->setUseAjax(true);
        $this->setDefaultSort('log_id');
        $this->setDefaultFilter(['empty' => 2]);
    }

    /**
     * Prepare Status Collection.
     *
     * @access protected
     * @return void
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('whbifrost/log_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns.
     *
     * @access protected
     * @return void
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'log_id',
            [
                'header'    => 'ID',
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'log_id',
            ]
        );

        $this->addColumn(
            'empty',
            [
                'header'    => 'Empty logs',
                'sortable'  => false,
                'type'      => 'options',
                'width'     => '100px',
                'options'   => [
                    1 => 'Only empty',
                    2 => 'Hide empty'
                ],
                'frame_callback'            => [$this, 'decorateEmptyLog'],
                'filter_condition_callback' => [$this, 'filterEmptyLog'],
            ]
        );

        $this->addColumn(
            'started_at',
            [
                'header'    => 'Started',
                'align'     =>'right',
                'width'     => '150px',
                'type'      => 'datetime',
                'index'     => 'started_at',
            ]
        );
        $this->addColumn(
            'completed_at',
            [
                'header'    => 'Completed',
                'align'     =>'right',
                'width'     => '150px',
                'type'      => 'datetime',
                'index'     => 'completed_at',
            ]
        );

        $this->addColumn(
            'running_time',
            [
                'header'    => 'Running Time',
                'align'     =>'right',
                'width'     => '100px',
                'index'     => 'started_at',
                'sortable'  => false,
                'filter'    => false,
                'frame_callback'    => [$this, 'decorateRunningTime']
            ]
        );

        $this->addColumn(
            'type',
            [
                'header'    => 'Type',
                'index'     => 'type',
                'type'      => 'options',
                'options'   => ['import' => 'Import', 'export' => 'Export']
            ]
        );

        $this->addColumn(
            'code',
            [
                'header'    => 'Code',
                'index'     => 'code',
            ]
        );


        foreach (['created', 'updated', 'skipped', 'not_found', 'deleted', 'errors'] as $code) {
            $this->addColumn(
                $code,
                [
                    'header'    => ucwords(str_replace('_', ' ', $code)),
                    'align'     => 'right',
                    'width'     => '40px',
                    'index'     => $code,
                    'type'      => 'number'
                ]
            );
        }

        $this->addColumn(
            'log',
            [
                'header'    => 'View Log',
                'index'     => 'log_id',
                'sortable'  => false,
                'filter'    => false,
                'frame_callback'    => [$this, 'decorateViewLog']
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Decorate empty log column.
     *
     * @param mixed $value
     * @param Webbhuset_Bifrost_Model_Log $row
     *
     * @return void
     */
    public function decorateEmptyLog($value, $row)
    {
        $total = $row->getCreated()
            + $row->getUpdated()
            + $row->getSkipped()
            + $row->getNotFound()
            + $row->getErrors();

        return $total ? '' : 'Empty';
    }

    /**
     * Filters empty log column.
     *
     * @param Webbhuset_Bifrost_Model_Resource_Log_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     *
     * @return void
     */
    public function filterEmptyLog($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if (!$value) {
            return $this;
        } else if ($value == 1) {
            $cond = ' = 0';
            $glue = ' AND ';
        } elseif ($value == 2) {
            $cond = ' > 0';
            $glue = ' OR ';
        }

        $types = ['created', 'updated', 'skipped', 'not_found', 'errors'];
        foreach ($types as $index => $type) {
            $types[$index] = "main_table.{$type}{$cond}";
        }
        $where = implode($glue, $types);

        $collection->getSelect()->where($where);

        return $this;
    }

    /**
     * Decorate running time column.
     *
     * @param mixed $value
     * @param Webbhuset_Bifrost_Model_Log $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param boolean $isExport
     *
     * @access public
     * @return string
     */
    public function decorateRunningTime($value, $row, $column, $isExport)
    {
        $startedAt   = $row->getStartedAt();

        if (!$startedAt) {
            return 'N/A';
        }

        $completedAt = $row->getCompletedAt();

        if ($completedAt) {
            $time = strtotime($completedAt);
        } else {
            $time = strtotime(Mage::getModel('core/date')->gmtDate());
        }

        $runningTime = $time - strtotime($startedAt);

        return Mage::helper('whpeasant')->formatTime($runningTime);
    }

    /**
     * Decorate view log column.
     *
     * @param mixed $value
     * @param Webbhuset_Bifrost_Model_Log $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param boolean $isExport
     *
     * @access public
     * @return string
     */
    public function decorateViewLog($value, $row, $column, $isExport)
    {
        $url = $this->getUrl('*/*/view', ['id' => $value]);

        return "<a href=\"{$url}\">View Log</a>";
    }


    /**
     * Returns row url.
     *
     * @param Webbhuset_Bifrost_Model_Log $row
     *
     * @access public
     * @return boolean
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Returns grid url.
     *
     * @access public
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }
}

