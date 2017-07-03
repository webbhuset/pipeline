<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;

class Export
{
    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
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
