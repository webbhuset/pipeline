<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend\Mock;
use Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend\BackendInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Repeater implements BackendInterface
{
    protected $oldData;
    protected $keyAttribute;
    public function __construct($params = null)
    {
        if (!isset($params['key_attribute'])) {
            throw new BifrostException("'key_attribute' parameter is not set.");
        }
        $this->keyAttribute = $params['key_attribute'];
    }

    public function getData($inputData)
    {
        $tmpData = [];
        $key = $inputData['new'][$this->keyAttribute];
        if (isset($this->oldData[$key])) {
            $inputData['old'] = $this->oldData[$key];
        }

        $this->oldData[$key] = $inputData['new'];

        return $inputData;
    }
}
