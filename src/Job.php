<?php

namespace Webbhuset\Bifrost\Core;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;

/**
 * Job
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
class Job
{
    protected $generator;
    protected $isDone;

    /**
     * Constructor.
     *
     * @param Webbhuset\Bifrost\Core\Component\ComponentInterface $component
     * @param mixed $input
     *
     * @return void
     */
    public function __construct(ComponentInterface $component, $input)
    {
        $this->generator    = $component->process($input);
        $this->isDone       = false;
    }

    /**
     * Process next. Returns true if there's more to process, else false.
     *
     * @return bool
     */
    public function processNext()
    {
        if ($this->isDone) {
            return false;
        }

        $this->generator->next();

        if (!$this->generator->valid()) {
            $this->isDone = true;
        }

        return !$this->isDone;
    }

    /**
     * Is processing done?
     *
     * @return bool
     */
    public function isDone()
    {
        return $this->isDone;
    }
}
