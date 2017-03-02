<?php

namespace Webbhuset\Bifrost\Core\Component\Dev;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;

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
                continue;
            }
        }
    }
}
