<?php

/**
 * HTML checkbox element block
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class Webbhuset_Bifrost_Block_Adminhtml_System_Config_Checkbox
    extends Mage_Core_Block_Abstract
{
    /**
     * Set element's HTML checked.
     *
     * @param string $checked
     *
     * @return Webbhuset_Bridge_Block_Adminhtml_System_Config_Html_Checkbox
     */
    public function setChecked($checked)
    {
        $this->setData('checked', ((bool)$checked ) ? 'checked="checked"' : '');
        return $this;
    }
    /**
     * Set element's HTML ID.
     *
     * @param string $id
     *
     * @return Webbhuset_Bridge_Block_Adminhtml_System_Config_Html_Checkbox
     */
    public function setId($id)
    {
        $this->setData('id', $id);
        return $this;
    }

    /**
     * Set element's CSS class.
     *
     * @param string $class
     *
     * @return Webbhuset_Bridge_Block_Adminhtml_System_Config_Html_Checkbox
     */
    public function setClass($class)
    {
        $this->setData('class', $class);
        return $this;
    }

    /**
     * Set element's name.
     *
     * @param string $name
     *
     * @return Webbhuset_Bridge_Block_Adminhtml_System_Config_Html_Checkbox
     */
    public function setName($name)
    {
        $this->setData('name', $name);
        return $this;
    }

    /**
     * Set element's HTML title.
     *
     * @param string $title
     *
     * @return Webbhuset_Bridge_Block_Adminhtml_System_Config_Html_Checkbox
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }

    /**
     * HTML checked of the element.
     *
     * @return string
     */
    public function getChecked()
    {
        return $this->getData('checked');
    }
    /**
     * HTML ID of the element.
     *
     * @return string
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * CSS class of the element.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getData('class');
    }

    /**
     * Returns HTML title of the element.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Returns HTML name of the element.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Render HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = sprintf(
            '<input type="checkbox" name="%s" id="%s" class="%s" title="%s" %s %s>',
            $this->getName(),
            $this->getId(),
            $this->getClass(),
            $this->getTitle(),
            $this->getExtraParams(),
            $this->getChecked()
        );

        return $html;
    }

    /**
     * Alias for toHtml().
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
