<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Map extends AbstractFunction
{
    protected $callback;

    public function __construct($callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        $this->callback = $callback;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $results = call_user_func($this->callback, $item);

            yield $results;
        }
    }
}
