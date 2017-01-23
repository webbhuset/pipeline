<?php

namespace Webbhuset\Bifrost\Core\Processor;

class Mute
{
    protected $label;
    public function __construct($label = 'dahbug')
    {
        $this->label = $label;
    }

    public function process($items)
    {
        return [];
    }
}
