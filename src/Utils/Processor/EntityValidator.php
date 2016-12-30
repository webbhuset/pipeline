<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;
use \Webbhuset\Bifrost\Core\Utils\Type\TypeInterface;

class EntityValidator extends AbstractProcessor
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
        $errors = $this->type->getErrors($data);
        if ($errors === false) {
            return $data;
        }

        if (is_string($errors)) {
            $this->logger->log($errors);
        }
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $this->logger->log($error);
            }
        }
    }
}
