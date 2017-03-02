<?php

namespace Webbhuset\Bifrost\Core\Component\Validate;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ErrorData;

class EntityWithSets implements ComponentInterface
{
    protected $sets;
    protected $setFieldName;

    public function __construct($sets, $setFieldName)
    {
        $this->sets         = $sets;
        $this->setFieldName = $setFieldName;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }

            $errors = $this->getErrors($item);

            if ($errors) {
                yield new ErrorData($item, $errors);
            } else {
                yield $item;
            }
        }
    }

    protected function getErrors($item)
    {
        if (!array_key_exists($this->setFieldName, $item)) {
            return "Item is missing attribute set: {$this->setFieldName}";
        }

        $setId  = $item[$this->setFieldName];

        if (!$setId) {
            return "Attribute set is empty";
        }

        if (!array_key_exists($setId, $this->sets)) {
            return "Attribute set '{$setId}' is not recognized";
        }

        $entity = $this->sets[$setId];

        return $entity->getErrors($item);
    }
}
