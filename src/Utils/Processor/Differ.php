<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\Utils\Type\TypeInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Differ extends AbstractProcessor
{
    protected $type;

    protected $castData = true;

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);
        if (!isset($params['type'])) {
            throw new BifrostException("Type parameter is not set.");
        }
        if (!$params['type'] instanceof TypeInterface) {
            throw new BifrostException("Type param must implement TypeInterface");
        }
        if (isset($params['cast_data'])) {
            if (!is_bool($params['cast_data'])) {
                throw new BifrostException("parameter 'cast_data' must be boolean");
            }
            $this->castData = $params['cast_data'];
        }

        $this->type = $params['type'];
    }

    protected function processData($data)
    {
        if ($this->castData) {
            $data['old'] = $this->type->cast($data['old']);
            $data['new'] = $this->type->cast($data['new']);
        }
        $test = $this->type->diff($data['old'], $data['new']);

        return $this->type->diff($data['old'], $data['new']);
    }

}
