<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\Observe\ObserverInterface;
use Webbhuset\Whaskell\WhaskellException;

class SplitApplyCombine extends AbstractFunction
{
    protected $splitFunction;
    protected $applyFunction;
    protected $combineFunction;

    public function __construct($splitFunction, $applyFunction, $combineFunction)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($splitFunction, 1);
        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        if (!$applyFunction instanceof AbstractFunction) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($applyFunction, 1);
            if ($canBeUsed !== true) {
                throw new WhaskellException($canBeUsed . ' Eg. function($item)');
            }
        }

        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($combineFunction, 2, false);
        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        $this->splitFunction    = $splitFunction;
        $this->applyFunction    = $applyFunction;
        $this->combineFunction  = $combineFunction;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $inputData = call_user_func($this->splitFunction, $item);

            $results = iterator_to_array(call_user_func($this->applyFunction, $inputData));

            yield call_user_func($this->combineFunction, $item, $results);
        }
    }

    public function registerObserver(ObserverInterface $observer)
    {
        if ($this->applyFunction instanceof AbstractFunction) {
            $this->applyFunction->registerObserver($observer);
        }
    }
}
