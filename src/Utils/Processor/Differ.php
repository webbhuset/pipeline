<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\Utils\Type\TypeInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Differ extends AbstractProcessor
{
    protected $type;

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);
        if (!isset($params['type'])) {
            throw new BifrostException("Type parameter is not set.");
        }
        if (!$params['type'] instanceof TypeInterface) {
            throw new BifrostException("Type param must implement TypeInterface");
        }

        $this->type = $params['type'];
    }

    protected function processData($data)
    {
        return $this->type->diff($data['old'], $data['new']);
    }

}