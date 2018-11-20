<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class ApplyCombine implements FunctionInterface
{
    protected $applyFunction;
    protected $combineFunction;


    public function __construct($applyFunction, $combineFunction)
    {
        if (is_array($applyFunction)) {
            $applyFunction = F::Compose($applyFunction);
        }
        if (!$applyFunction instanceof AbstractFunction) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($applyFunction, 1);
            if ($canBeUsed !== true) {
                throw new WhaskellException('$applyFunction: ' . $canBeUsed . ' e.g. function($item)');
            }
        }

        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($combineFunction, 2, false);
        if ($canBeUsed !== true) {
            throw new WhaskellException('$combineFunction: ' . $canBeUsed . ' e.g. function($item, $results)');
        }

        $this->applyFunction    = $applyFunction;
        $this->combineFunction  = $combineFunction;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $results = call_user_func($this->applyFunction, [$item]);

            yield call_user_func($this->combineFunction, $item, $results);
        }
    }
}
