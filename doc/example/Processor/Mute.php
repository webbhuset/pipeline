<?php

namespace Webbhuset\Bifrost\Processor;

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
