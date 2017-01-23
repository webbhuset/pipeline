<?php

namespace Webbhuset\Bifrost\Core\Component\Dev;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;

class Mute implements ComponentInterface
{
    public function process($items)
    {
        return [];
    }
}
