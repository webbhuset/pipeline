<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractReader implements ReaderInterface
{
    protected $log;
    protected $nextStep;


    public function __construct(Utils\Logger\LoggerInterface $log, $nextStep, $params)
    {
        if (!$nextStep instanceof Utils\Processor\ProcessorInterface
            && !$nextStep instanceof Utils\Writer\WriterInterface
        ) {
            throw new BifrostException('Next step must implement ProcessorInterface or WriterInterface');
        }

        $this->log          = $log;
        $this->nextStep     = $nextStep;
    }

    public function processNext()
    {
        $data = $this->getData();

        if (!$data) {
            return false;
        }

        $this->nextStep->processNext($data);

        return true;
    }

    public function getEntityCount()
    {
        while ($data = $this->getData()) {
            $this->nextStep->processNext($data, true);
        }

        $count = $this->nextStep->count();

        $this->nextStep->finalize(true);

        return $count;
    }

    public function finalize()
    {
        $this->nextStep->finalize();
    }

    public function init($args)
    {
        $this->nextStep->init();
    }

    abstract protected function getData();
}
