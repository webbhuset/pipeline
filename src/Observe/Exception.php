<?php

namespace Webbhuset\Whaskell\Observe;

use Exception as PhpException;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Exception extends AbstractObserver
{
    protected $callback;


    public function __construct($function, $callback)
    {
        parent::__construct($function);

        if (!$function) {
            throw new WhaskellException('Observer function cannot be null.');
        }

        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 3, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' E.g. function($exception, $observer, $item)');
        }

        $this->callback = $callback;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            try {
                $newItems = call_user_func($this->function, [$item], false);

                foreach ($newItems as $newItem) {
                    yield $newItem;
                }
            } catch (PhpException $e) {
                call_user_func($this->callback, $e, $this, $item);

                throw $e;
            }
        }

        if ($finalize) {
            try {
                $newItems = call_user_func($this->function, [], true);

                foreach ($newItems as $newItem) {
                    yield $newItem;
                }
            } catch (PhpException $e) {
                call_user_func($this->callback, $e, $this, null);

                throw $e;
            }
        }
    }
}
