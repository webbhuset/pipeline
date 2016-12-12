<?php

class Task
{
    protected $source;
    protected $dest;
    protected $validator;
    protected $logger;
    protected $progress;

    protected $isDone = false;

    public function __construct($source, $dest, $validator, $logger)
    {
        $this->source       = $source;
        $this->dest         = $dest;
        $this->validator    = $validator;
        $this->logger       = $logger;
        $this->progress     = new Progress;
    }

    public function init($filename, $args)
    {
        $this->source->init($filename, $args);
        $this->dest->init($args);
        $this->validator->init($args);
        $this->logger->init($filename, $args);
        $this->progress->init($args);

        $count = $this->source->getEntityCount();
        $this->progress->setTotal($count);
    }

    public function processOne()
    {
        try {
            $data = $this->source->getNextEntity();

            if ($data === false) {
                $this->isDone = true;
                return;
            }

            $data = $this->validator->wash($data);
            $this->validator->validate($data);

            $this->dest->putEntity($data);
        } catch (Exception $e) {
            $this->logger->write($e->getMessage());
        }
    }

    public function finalize()
    {
        $this->source->finalize();
        $this->dest->finalize();
        $this->validator->finalize();
        $this->logger->finalize();
        $this->progress->finalize();
    }

    public function isDone()
    {
        return $this->isDone;
    }
}
