<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

class WriterFactory
{
    protected $interface = 'Webbhuset\Bifrost\Core\Utils\Writer\WriterInterface';
    protected $class;
    protected $params;

    /**
     * Constructor.
     *
     * @param string $class
     * @param array $params
     *
     * @return void
     */
    public function __construct($class, $params)
    {
        if (!class_exists($class)) {
            throw new BifrostException("Class {$class} not found.");
        }
        if (!in_array($this->interface, class_implements($class))) {
            throw new BifrostException("Class must implement {$this->interface}.");
        }
        $this->class    = $class;
        $this->params   = $params;
    }

    public function create(Utils\Logger\LoggerInterface $logger, $nextSteps)
    {
        $className = $this->class;

        return new $className($logger, $this->params);
    }
}
