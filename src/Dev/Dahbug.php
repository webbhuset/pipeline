<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Dahbug extends AbstractFunction
{
    protected $backtrace;
    protected $log;
    protected $label;
    protected $depth;

    public function __construct(array $backtrace = null, $label = 'dump', $depth = 10)
    {
        $this->backtrace    = $backtrace ?: debug_backtrace();
        $this->log          = class_exists('\dahbug');
        $this->label        = $label;
        $this->depth        = $depth;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($this->log) {
                LocalDahbug::dumpWithBacktrace($this->backtrace, $item, $this->label, $this->depth);
            }
            yield $item;
        }
    }
}

if (class_exists('\dahbug')) {
    class LocalDahbug extends \dahbug
    {
        static public function dumpWithBacktrace($backtrace, $var, $label = null, $maxDepth = null)
        {
            self::$_backtrace = $backtrace;

            return self::dump($var, $label, $maxDepth);
        }

        static public function dump($var, $label = null, $maxDepth = null)
        {
            if (!is_int($maxDepth)) {
                $maxDepth = self::getData('max_depth');
            }

            if (self::getData('print_filename')) {
                self::_printFilename();
            }

            $label = self::_prepareLabel($label, 'label');
            $string = self::_formatVar($var, 0, $maxDepth);
            $string .= DAHBUG_EOL;
            self::_write($label . ' = ' . $string);

            return $var;
        }
    }
}
