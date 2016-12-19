<?php
namespace Webbhuset\Bifrost\Core\Job;

class Task implements Task\TaskInterface
{
    protected $source;
    protected $dest;
    protected $validator;
    protected $logger;
    protected $progress;

    protected $isDone = false;

    public function __construct($params)
    {
        $this->source       = $params['source'];
        $this->dest         = $params['destination'];
        $this->type         = $params['type'];
        $this->logger       = $params['logger'];
        $this->progress     = new Progress;
    }

    public function init($args)
    {
        $this->source->init($args);
        $this->dest->init($args);
        $this->logger->init($args);
        $this->progress->init($args);

        $count = $this->source->getEntityCount();
        $this->progress->setTotal($count);
    }

    public function processNext()
    {
        try {
            $data = $this->source->getNextEntity();

            if ($data === false) {
                $this->isDone = true;
                return;
            }
            $this->type->getErrors($data);

            $this->dest->putEntity($data);
        } catch (Exception $e) {
            $this->logger->write($e->getMessage());
        }
    }

    public function finalize()
    {
        $this->source->finalize();
        $this->dest->finalize();
        $this->logger->finalize();
        $this->progress->finalize();
    }

    public function isDone()
    {
        return $this->isDone;
    }

    public function getProgress()
    {
        return $this->progress;
    }
}
