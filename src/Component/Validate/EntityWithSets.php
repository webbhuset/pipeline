<?php

namespace Webbhuset\Bifrost\Core\Component\Validate;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\BifrostException;

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
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }

            $errors = $this->getErrors($item);

            if ($errors) {
                $item = new Data\Error($errors, $item);
                yield 'event' => new Data\Reference($item, 'error');
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
