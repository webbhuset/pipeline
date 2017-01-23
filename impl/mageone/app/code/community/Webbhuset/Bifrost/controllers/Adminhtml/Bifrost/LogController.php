<?php
/**
 * Adminhtml bifrost log controller
 *
 * @package Webbhuset
 * @module  Webbhuset_Bifrost
 * @author  Webbhuset <info@webbhuset.se>
 */
class Webbhuset_Bifrost_Adminhtml_Bifrost_LogController
    extends Mage_Adminhtml_Controller_Action
{
    /*
     * Index action, log grid container
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
     * Grid action, used for ajax when filtering.
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    /*
     * View action, log message grid container
     *
     * @return void
     */
    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    /*
     * Message grid action, used for ajax when filtering.
     *
     * @return void
     */
    public function messagegridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
