<?php

/**
 * Cron Picker.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class Webbhuset_Bifrost_Block_Adminhtml_System_Config_Cron
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Holds the uniqe id based on the config xml path.
     *
     * @var string
     */
    protected $_id;

    /**
     * Holds all element values.
     *
     * @var array
     */
    protected $_values;

    /**
     * Select field renderer.
     *
     * @var Mage_Core_Html_Select
     */
    protected $_selectFieldRenderer;

    /**
     * Checkbox field renderer.
     *
     * @var Webbhuset_Marketing_Block_Adminhtml_System_Config_Checkbox
     */
    protected $_checkboxFieldRenderer;


    /**
     * Adds multiselect boxes for creating a custom cron schedule based on minute, hour, day, month and weekday.
     *
     * @param   Varien_Data_Form_Element_Abstract $element
     *
     * @return  string $html
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $this->_id = $element->getId();
        $data = $element->getValue();
        $this->_values = $element->getValue();

        $selectElement = implode(
            '</td><td style="padding-right:3px;">',
            $this->_getCronSelectElement()
        );
        $expression = $this->_getCronExpressionHtml();
        $javascript = $this->_getJs();

        $html = "
            <table>
                <tr>
                    <td style=\"padding-right:3px;\">
                        {$selectElement}
                    </td>
                </tr>
                {$expression}
            </table>
            {$javascript}
            <input type=\"hidden\" id=\"{$this->_id}\" />
        ";

        return $html;
    }

    /**
     * Get select block.
     *
     * @return Mage_Core_Html_Select
     */
    protected function _getSelectFieldRenderer()
    {
        if (!$this->_selectFieldRenderer) {
            $this->_selectFieldRenderer = $this->getLayout()
                ->createBlock('core/html_select');
        }
        return $this->_selectFieldRenderer;
    }

    /**
     * Get checkbox block.
     *
     * @return Webbhuset_Marketing_Block_Adminhtml_System_Config_Checkbox
     */
    protected function _getCheckboxFieldRenderer()
    {
        if (!$this->_checkboxFieldRenderer) {
            $this->_checkboxFieldRenderer = $this->getLayout()
                ->createBlock('whbifrost/adminhtml_system_config_checkbox');
        }
        return $this->_checkboxFieldRenderer;
    }

    /**
     * Renders select field html with specified options and name.
     *
     * @param string $name
     * @param array $options
     *
     * @return string
     */
    protected function _renderCronSelectTemplate($name, $options = array())
    {
        $param = (isset($this->_values[$this->_id][$name . '_every'])) ? 'disabled' : '';

        return $this->_getElementHeader($name)
            . $this->_getSelectFieldRenderer()
                ->setName($this->_getInputName($name) . '[]')
                ->setId("{$this->_id}_{$name}")
                ->setClass('multiselect')
                ->setExtraParams('multiple="multiple" ' . $param . ' style="width:100px; height: 173px; margin-top:5px;"')
                ->setOptions($options)
                ->setValue((isset($this->_values[$this->_id][$name])) ? $this->_values[$this->_id][$name] : array())
                ->toHtml();
    }

    /**
     * Returns html of rendered cron selectors and the table.
     *
     * @return array
     */
    public function _getCronSelectElement()
    {
        $parts = [
            $this->_renderCronSelectTemplate("minute", $this->_optionMinute()),
            $this->_renderCronSelectTemplate("hour", $this->_optionHour()),
            $this->_renderCronSelectTemplate("day", $this->_optionDay()),
            $this->_renderCronSelectTemplate("month", $this->_optionMonth()),
            $this->_renderCronSelectTemplate("weekday", $this->_optionWeekday()),
        ];

        return $parts;
    }

    /**
     * Returns html containing title.
     *
     * @param   string $name
     *
     * @return  string $html
     */
    protected function _getElementLabel($name)
    {
        return "<h4>" . Mage::helper('whbifrost')->__(ucfirst($name)) . "</h4>";
    }

    /**
     * Returns html with checkbox for event triggering.
     *
     * @param   string $name
     *
     * @return  string $html
     */
    protected function _getElementSelectors($name)
    {
        $name       = strtolower($name);
        $inputName  = $this->_getInputName($name . '_every');
        $html       = '';
        $html      .= $this->_getCheckboxFieldRenderer()
            ->setName($inputName)
            ->setId("{$this->_id}_{$name}_checkbox")
            ->setClass("cronevery {$this->_id}")
            ->setExtraParams('style="margin-right:5px;"')
            ->setChecked((isset($this->_values[$this->_id][$name . '_every'])) ? true : false)
            ->toHtml();

        $html .= '<label for="' . $this->_id . "_" . $name . "_checkbox" . '">' .
            Mage::helper('whbifrost')->__('Every ' . $name) . '</label>';

        return $html;
    }

    /**
     * Returns the element name.
     *
     * @param   string $name
     *
     * @return  string
     */
    protected function _getInputName($name)
    {
        return $this->getElement()->getName() . '[' . $this->_id . '][' . $name . ']';
    }

    /**
     * Returns html containing all header parts over each multiselect.
     *
     * @param   string $name
     *
     * @return  string $html
     */
    protected function _getElementHeader($name)
    {
        $html  = $this->_getElementLabel($name);
        $html .= $this->_getElementSelectors($name);

        return $html;
    }

    /**
     * Returns js for all event triggering and populating of data.
     *
     * @return  string
     */
    protected function _getJs()
    {
        return "<script type='text/javascript'>
                    //<![CDATA[
                        document.observe('dom:loaded', function() {
                            var id = '" . $this->_id . "';

                            $$('.cronevery.'+id).invoke('observe', 'change', function(event) {
                                var name = this.id.substr(0,(this.id.length - '__checkbox'.length + 1));

                                if (this.checked) {
                                    enableDisable(name, false);
                                } else {
                                    enableDisable(name, true);
                                }
                            });

                            function enableDisable(name, enableDisable)
                            {
                                if (enableDisable == true) {
                                    $(name).enable();
                                } else {
                                    $(name).disable();
                                }
                            }
                        });
                    //]]>
                </script>";
    }

    /**
     * Returns source to be used as multiselect options.
     *
     * @return  array, $arr
     */
    protected function _optionMinute()
    {
        $arr = array();
        for ($i = 0; $i <= 59; $i++) {
            $arr[] = array('value' => $i, 'label' => $i);
        }
        return $arr;
    }

    /**
     * Returns source to be used as multiselect options.
     *
     * @return  array $arr
     */
    protected function _optionHour()
    {
        $arr = array();
        for ($i = 0; $i <= 23; $i++) {
            $arr[] = array(
                'value' => $i,
                'label' => str_pad($i, 2, "0", STR_PAD_LEFT).":00"
            );
        }
        return $arr;
    }

    /**
     * Returns source to be used as multiselect options.
     *
     * @return  array $arr
     */
    protected function _optionDay()
    {
        $arr = array();
        for ($i = 1; $i <= 30; $i++) {
            $arr[] = array('value' => $i, 'label' => $i);
        }
        return $arr;
    }

    /**
     * Returns source to be used as multiselect options.
     *
     * @return  array
     */
    protected function _optionMonth()
    {
        return array(
            array(
                'label' =>  Mage::helper('whbifrost')->__('January'),
                'value' => 1
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('February'),
                'value' => 2
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('March'),
                'value' => 3
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('April'),
                'value' => 4
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('May'),
                'value' => 5
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('June'),
                'value' => 6
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('July'),
                'value' => 7
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('August'),
                'value' => 8
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('September'),
                'value' => 9
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('October'),
                'value' => 10
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('November'),
                'value' => 11
            ),
            array(
                'label' =>  Mage::helper('whbifrost')->__('December'),
                'value' => 12
            ),
        );
    }

    /**
     * Returns source to be used as multiselect options.
     *
     * @return  array
     */
    protected function _optionWeekday()
    {
        return Mage::app()->getLocale()->getOptionWeekdays();
    }

    /**
     * Returns the method to run from the system.xml file for for the element.
     *
     * @return  string|null
     */
    protected function _getMethod()
    {
        $data = $this->getElement()->getData('original_data');
        if (isset($data['cron_run'])) {
            return $data['cron_run'];
        }
        return;
    }

    /**
     * Retuns a part of the string job expression.
     *
     * @param   string $index
     *
     * @return  string $string
     */
    protected function _getCronExprPart($index = "")
    {
        $string = "*";
        $cronArr = $this->_values;
        if (isset($cronArr[$this->_id][$index]) && is_array($cronArr[$this->_id][$index])) {
            $string = implode(",", $cronArr[$this->_id][$index]);
        }

        return $string;
    }

    /**
     * Returns the complete cron job expression.
     *
     * @return  string
     */
    public function _getExpr()
    {
        return implode(' ', [
            $this->_getCronExprPart("minute"),
            $this->_getCronExprPart("hour"),
            $this->_getCronExprPart("day"),
            $this->_getCronExprPart("month"),
            $this->_getCronExprPart("weekday")
        ]);
    }

    /**
     * Returns html with the current setup of cron expression.
     *
     * @return  string $html
     */
    protected function _getCronExpressionHtml()
    {
        if ($this->_containsValues()) {
            return '<tr><td colspan="5"><span style="font-size: 11px; font-weight: bold;">'
                . Mage::helper('whbifrost')->__("Cron expression") . ': '
                . '<span style="word-spacing: 10px;">' . $this->_getExpr() . '</span></span></td></tr>';
        } else {
            return '';
        }
    }

    /**
     * Returns a boolean response if the saves values contains the required information.
     *
     * @return  boolean $validate
     */
    public function _containsValues()
    {
        $validate = true;
        $cronArr = $this->_values;
        if (isset($cronArr[$this->_id]) && is_array($cronArr[$this->_id])) {
            foreach ($cronArr[$this->_id] as $data) {
                if ((!is_array($data) && !is_string($data)) || (is_array($data) && count($data) < 1)) {
                    $validate = false;
                }
            }
        } else {
            $validate = false;
        }

        return $validate;
    }
}
