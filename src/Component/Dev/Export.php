<?php

namespace Webbhuset\Bifrost\Core\Component\Dev;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;

class Export implements ComponentInterface
{
    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            echo var_export($item) . ",\n";
            yield $item;
        }
    }

    public function finalize($items = [])
    {
        return $items;
    }
}
