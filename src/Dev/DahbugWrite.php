<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class DahbugWrite implements FunctionInterface
{
    protected $log;

    public function __construct()
    {
        $this->log = class_exists('\dahbug');
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($this->log) {
                \dahbug::write($item);
            }

            yield $item;
        }
    }
}
