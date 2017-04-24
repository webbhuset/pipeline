<?php

namespace Webbhuset\Bifrost\Component\Dev;

use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;

class Mute implements ComponentInterface
{
    protected $muteActions;

    public function __construct($muteActions = false) {
        $this->muteActions = $muteActions;
    }

    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface && !$this->muteActions) {
                yield $item;
            }
        }
    }
}
