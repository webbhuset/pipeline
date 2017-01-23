<?php

namespace Webbhuset\Bifrost\Core\Component\Validate;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\BifrostException;

class Entity implements ComponentInterface
{
    protected $entity;

    public function __construct(Type\TypeInterface $entity)
    {
        $this->entity = $entity;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            $errors = $this->entity->getErrors($item);

            if ($errors) {
                $item = new Data\Error($errors, $item);
                yield 'event' => new Data\Reference($item, 'error');
            } else {
                yield $item;
            }
        }
    }
}
