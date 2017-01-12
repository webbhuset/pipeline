<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend\Mock;
use \Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Utils\Processor\AbstractProcessor;


class Repeater extends AbstractProcessor
{
    protected $oldData = [];
    protected $tmpData = [];

    protected $keyAttribute;

    public function __construct($params = null)
    {
        if (!isset($params['key_attribute'])) {
            throw new BifrostException("'key_attribute' parameter is not set.");
        }
        $this->keyAttribute = $params['key_attribute'];
    }

    public function getData()
    {
        $result = [];
        foreach ($this->oldData as $key => $item) {
            $result[$key]['old'] = $this->oldData[$key];
        }
        $this->oldData = $this->tmpData;
        $this->tmpData = [];

        return $result;
    }

    public function processData($data)
    {
        $key                 = $data['new'][$this->keyAttribute];
        $this->tmpData[$key] = $data['new'];
    }
}
