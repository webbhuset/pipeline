<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;

class Dahbug
{
    protected $events;
    protected $label;
    protected $log;
    protected $backtrace;

    public function __construct($label = 'dump', $events = false, $log = true)
    {
        $this->events       = $events;
        $this->label        = $label;
        $this->log          = $log;
        $this->backtrace    = debug_backtrace();
    }

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface && !$this->events) {
                yield $item;
                continue;
            }
            if ($this->log) {
                LocalDahbug::dumpWithBacktrace($this->backtrace, $item, $this->label, 10);
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
