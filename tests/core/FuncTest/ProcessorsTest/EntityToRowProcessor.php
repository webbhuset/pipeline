<?php
namespace Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest;
use Webbhuset\Bifrost\Utils\Logger\LoggerInterface;
use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Utils\Processor\AbstractProcessor;

class EntityToRowProcessor extends AbstractProcessor
{
    protected $attributes;
    public function __construct(LoggerInterface $logger, $nextStep, $params)
    {
        parent::__construct($logger, $nextStep, $params);

        if (!isset($params['attributes'])) {
            throw new BifrostException("'attributes' parameter is not set.");
        }
        $this->attributes = $params['attributes'];
    }

    public function processNext($entities, $onlyForCount = false)
    {
        $rows = [];

        foreach ($entities as $data) {
            if (isset($data['sku']['+'])) {
                $id = $data['sku']['+'];
            }
            unset($data['sku']);
            foreach ($data as $code => $diff) {
                if (isset($this->attributes[$code])) {
                    $rows[] = [
                        'entity_type_id'    => 4,
                        'store_id'          => 0,
                        'attribute_id'      => $this->attributes[$code],
                        'entity_id'         => $id,
                        'value'             => $diff['+'],
                    ];
                }
            }
        }

        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($rows, $onlyForCount);
        }
    }

    protected function processData($data)
    {
    }
}
