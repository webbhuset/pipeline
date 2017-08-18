<?php

namespace Webbhuset\Whaskell\Dispatch;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;
use Webbhuset\Whaskell\FunctionSignature;

class Error
{
    protected $callback;

    public function __construct($callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        $this->callback = $callback;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }

            $result = call_user_func($this->callback, $item);
            if ($result) {
                yield new ErrorData($item, $result);
            } else {
                yield $item;
            }
        }
    }
}

