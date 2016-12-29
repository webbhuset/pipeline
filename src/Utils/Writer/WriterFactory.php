<?php
namespace Webbhuset\Bifrost\Core\Writer;
use Webbhuset\Bifrost\Core\Utils as Utils;
use Webbhuset\Bifrost\Core\Utils\Processor\ProcessorFactory;
use Webbhuset\Bifrost\Core\BifrostException;

class WriterFactory extends ProcessorFactory
{
    protected $interface = 'WriterInterface';


    public function create(Utils\Logger\LoggerInterface $log, $nextChain = null)
    {
        if ($nextChain != null) {
            throw new BifrostException('Writer must be last in chain.');
        }

        $className = $this->class;

        return new $className($log, $params);
    }
}
