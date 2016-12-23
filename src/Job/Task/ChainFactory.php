<?php
namespace Webbhuset\Bifrost\Core\Utils\Factory;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

/**
 * Chain factory.
 *
 * @uses      Factory
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class ChainFactory
{
    /**
     * Factories.
     *
     * @var array
     */
    protected $factories;

    /**
     * Log.
     *
     * @var Utils\Log\LogInterface
     */
    protected $log;


    /**
     * Constructor.
     *
     * @param Utils\Log\LogInterface $log
     * @param array $factories
     *
     * @return void
     */
    public function __construct(Utils\Log\LogInterface $log, array $factories)
    {
        if (!reset($factories) instanceof ReaderFactory) {
            throw new BifrostException('The first factory must be a ReaderFactory');
        }
        if (!end($factories) instanceof WriterFactory) {
            throw new BifrostException('The last factory must be a WriterFactory');
        }

        $this->log          = $log;
        $this->factories    = $factories;
    }

    /**
     * Factory create.
     *
     * @return Reader
     */
    public function create()
    {
        $previous = null;
        foreach ($this->factories as $factory) {
            $previous = $factory->create($this->log, $previous);
        }

        return $previous;
    }
}
