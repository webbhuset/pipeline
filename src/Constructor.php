<?php

namespace Webbhuset\Whaskell;

use Webbhuset\Whaskell\Convert;
use Webbhuset\Whaskell\Dispatch;
use Webbhuset\Whaskell\Dev;
use Webbhuset\Whaskell\Flow;
use Webbhuset\Whaskell\IO;
use Webbhuset\Whaskell\Iterable as Iterables;
use Webbhuset\Whaskell\Observer;
use Webbhuset\Whaskell\Validate;

class Constructor
{
    // Flow

    public static function Compose(array $functions)
    {
        return new Flow\Compose($functions);
    }

    // IO
}
