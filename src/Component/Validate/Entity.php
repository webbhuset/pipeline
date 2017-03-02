<?php

namespace Webbhuset\Bifrost\Core\Component\Validate;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ErrorData;
use Webbhuset\Bifrost\Core\Type\TypeInterface;

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
