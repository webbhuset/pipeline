<?php

/**
 *  Message grid container.
 *
 * @copyright Copyright (C) 2015 Webbhuset AB
 * @author Webbhuset AB <info@webbhuset.se>
 */
class Webbhuset_Bifrost_Block_Adminhtml_Message
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
        $logId = Mage::app()->getRequest()->getParam('id');

        $this->_controller = 'adminhtml_message';
        $this->_blockGroup = 'whbifrost';
        $this->_headerText = $this->__("Bifrost Log #{$logId} Messages");

        parent::__construct();
        $this->removeButton('add');

        $url = Mage::helper("adminhtml")->getUrl("*/*/index");
        $this->addButton(
            'back',
            [
                'label'     => 'Back',
                'onclick'   => "setLocation('{$url}')",
                'class'     => 'back',
            ]
        );
    }
}

