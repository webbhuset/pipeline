<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend;

class DefaultValues implements BackendInterface
{
    protected $defaultValues;

    public function __construct($params)
    {
        if (!isset($params['default_values'])) {
            throw new BifrostException("'default_values' parameter is not set.");
        }
        $this->defaultValues = $params['default_values'];
    }

    public function getData($inputData) {
        return $this->defaultValues;
    }
}
