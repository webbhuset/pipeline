<?php

namespace Webbhuset\Whaskell;

interface FunctionInterface
{
    public function __invoke($items, $keepState = false);
}
