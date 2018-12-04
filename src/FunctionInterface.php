<?php

namespace Webbhuset\Pipeline;

interface FunctionInterface
{
    public function __invoke($items, $keepState = false);
}
