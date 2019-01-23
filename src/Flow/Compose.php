<?php

namespace Webbhuset\Pipeline\Flow;

use Webbhuset\Pipeline\FunctionInterface;

class Compose implements FunctionInterface
{
    protected $functions;


    public function __construct(array $functions)
    {
        $this->functions = $this->flatten($functions);
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($this->functions as $function) {
            $values = $function($values, $keepState);
        }

        return $values;
    }

    protected function flatten(array $functions): array
    {
        $flattened = [];

        foreach ($functions as $function) {
            if (is_array($function)) {
                $flattened = array_merge($flattened, $this->flatten($function));
            } elseif ($function instanceof self) {
                $flattened = array_merge($flattened, $function->getFunctions());
            } elseif ($function instanceof FunctionInterface) {
                $flattened[] = $function;
            } else {
                $class = is_object($function) ? get_class($function) : $function;

                throw new \InvalidArgumentException("Input function {$class} does not implement FunctionInterface.");
            }
        }

        return $flattened;
    }

    protected function getFunctions(): array
    {
        return $this->functions;
    }
}
