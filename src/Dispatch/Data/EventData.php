<?php

namespace Webbhuset\Whaskell\Dispatch\Data;

class EventData implements DataInterface
{
    protected $name;
    protected $item;
    protected $data;
    protected $contexts;

    public function __construct($name, $item, $data = [], array $contexts = [])
    {
        $this->name     = $name;
        $this->item     = $item;
        $this->data     = $data;
        $this->contexts = $contexts;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getContexts()
    {
        return $this->contexts;
    }

    public function appendContext($context)
    {
        $contexts   = $this->contexts;
        $contexts[] = $context;

        return new static($this->name, $this->item, $this->data, $contexts);
    }
}
