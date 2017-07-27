<?php

namespace Webbhuset\Whaskell\Observe;

use Exception as PhpException;
use Webbhuset\Whaskell\WhaskellException;

class Exception extends AbstractObserver
{
    protected $callback;

    public function __construct($function, $callback)
    {
        parent::__construct($function);

        $this->callback = $callback;
    }

    protected function invoke($items, $finalize = true)
    {
        if (!$this->function) {
            throw new WhaskellException('Observer function cannot be null.');
        }

        $item = null;
        try {
            $items = call_user_func($this->function, $items, $finalize);
            foreach ($items as $item) {
                yield $item;
            }
        } catch (PhpException $e) {
            call_user_func($this->callback, $e, $this, $item);

            throw $e;
        }
    }
}
