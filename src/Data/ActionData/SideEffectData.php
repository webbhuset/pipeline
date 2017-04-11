<?php

namespace Webbhuset\Bifrost\Data\ActionData;

class SideEffectData implements ActionDataInterface
{
    protected $name;
    protected $item;
    protected $data;

    public function __construct($name, $item, $data = [])
    {
        $this->name     = $name;
        $this->item     = $item;
        $this->data     = $data;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function getData()
    {
        return $this->data;
    }
}
