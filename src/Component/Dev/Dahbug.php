<?php

namespace Webbhuset\Bifrost\Core\Component\Dev;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;

class Dahbug implements ComponentInterface
{
    protected $label;
    protected $log = true;

    public function __construct($label = 'dump', $log = true)
    {
        $this->label = $label;
        $this->log = $log;
    }

    public function process($items)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            if ($this->log) {
                \dahbug::dump($item, $this->label, 10);
            }
            yield $key => $item;
        }
    }
}
