<?php

/**
 * Status grid container.
 *
 * @copyright Copyright (C) 2015 Webbhuset AB
 * @author Webbhuset AB <info@webbhuset.se>
 */
class Webbhuset_Bifrost_Block_Adminhtml_Log
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_log';
        $this->_blockGroup = 'whbifrost';
        $this->_headerText = $this->__('Bifrost Logs');

        parent::__construct();
        $this->removeButton('add');

    }
}

