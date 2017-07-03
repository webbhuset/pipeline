<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;

class Mute
{
    protected $muteActions;

    public function __construct($muteActions = false) {
        $this->muteActions = $muteActions;
    }

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface && !$this->muteActions) {
                yield $item;
            }
        }
    }
}
