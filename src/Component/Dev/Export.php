<?php

namespace Webbhuset\Bifrost\Core\Component\Dev;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;

class Export implements ComponentInterface
{
    public function process($items)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            echo var_export($item) . ",\n";
            yield $key => $item;
        }
    }

    public function finalize($items = [])
    {
        return $items;
    }
}
