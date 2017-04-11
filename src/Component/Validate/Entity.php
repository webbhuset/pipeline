<?php

namespace Webbhuset\Bifrost\Component\Validate;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Data\ActionData\ErrorData;
use Webbhuset\Bifrost\Type\TypeInterface;

class Entity implements ComponentInterface
{
    protected $entity;

    public function __construct(TypeInterface $entity)
    {
        $this->entity = $entity;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            $errors = $this->entity->getErrors($item);

            if ($errors) {
                yield new ErrorData($item, $errors);
            } else {
                yield $item;
            }
        }
    }
}
