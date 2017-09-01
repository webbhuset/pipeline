<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class DahbugWrite extends AbstractFunction
{
    protected $log;

    public function __construct()
    {
        $this->log = class_exists('\dahbug');
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($this->log) {
                \dahbug::write($item);
            }

            yield $item;
        }
    }
}
