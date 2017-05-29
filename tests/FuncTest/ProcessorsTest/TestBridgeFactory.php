<?php
namespace  Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest;
use Webbhuset\Bifrost\Job\Task\BridgeFactory;
use Webbhuset\Bifrost\Utils\Writer\WriterFactory;
use Webbhuset\Bifrost\Utils\Processor\ProcessorFactory;
use Webbhuset\Bifrost\Utils\Reader\ReaderFactory;
use Webbhuset\Bifrost\Utils\ValueConverter\StringToInt;
use Webbhuset\Bifrost\Utils\ValueConverter\StringToFloat;
use Webbhuset\Bifrost\Utils\Processor\Filler\Backend\DefaultValues;
use Webbhuset\Bifrost\Utils\Processor\Filler\Backend\Mock\Repeater;
use Webbhuset\Bifrost\Utils\Type as Type;

class TestBridgeFactory extends BridgeFactory
{
    protected $bridgeSpecification =  <<<BRIDGE
reader->mapper->converter->defaultFiller->entityValidator->batcher->oldDataFiller->differ
    ->createFilter->batcher
        ->varcharMapper->varcharInserter1
        ->intMapper->intInserter1
        ->decimalMapper->decimalInserter1
        ->textMapper->textInserter1
        ->datetimeMapper->datetimeInserter1
    ->updateFilter
        ->varcharMapper->batcher->varcharInserter2
        ->intMapper->batcher->intInserter2
        ->decimalMapper->batcher->decimalInserter2
        ->textMapper->batcher->textInserter2
        ->datetimeMapper->batcher->datetimeInserter2
BRIDGE;

    protected function getType()
    {
        $typeParams = [
            'entity_id_field' => 'sku',
            'fields'          => [
                'name'              =>  new Type\StringType(),
                'sku'               =>  new Type\StringType(),
                'description'       =>  new Type\StringType(),
                'price'             =>  new Type\FloatType(),
                'status'            =>  new Type\IntType(),
                'news_from_date'    =>  new Type\StringType(),
            ]
        ];

        return new Type\EntityType($typeParams);
    }

    public function reader()
    {
        return new ReaderFactory('Webbhuset\Bifrost\Utils\Reader\Mock\UserDefined', []);
    }

    public function mapper()
    {
        $fields = [
            'name'              => '/test1/namn',
            'sku'               => '/test2/artikelnummer',
            'description'       => '/test1/beskrivning',
            'price'             => '/test2/pris',
            'status'            => '/test2/status',
            'news_from_date'    => '/test2/datum',
        ];
        $params = [
            "fields" => $fields,
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Mapper', $params);
    }

    public function converter()
    {
        $fields = [
            'name'              => null,
            'sku'               => null,
            'description'       => null,
            'price'             => new StringToFloat,
            'status'            => new StringToInt,
            'news_from_date'    => null,
        ];
        $params = [
            "fields" => $fields,
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Converter', $params);
    }

    public function defaultFiller()
    {
        $defaults = [
            'news_from_date' => '2017-01-02 00:00:00',
        ];
        $backendParams = [
            'default_values' => $defaults,
        ];
        $params = [
            'backend'           => new DefaultValues($backendParams),
            'key_specification' => ['sku'],
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Filler', $params);
    }

    public function entityValidator()
    {
        $params = [
            'type' => $this->getType(),
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\EntityValidator', $params);
    }

    public function batcher()
    {
        $params = [
            'batch_size'   => 1,
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Batcher', $params);
    }

    public function oldDataFiller()
    {
        $fields       = [$this, "mapToOldNewFormat"];
        $mapperParams = [
            "fields"            => $fields,
        ];
        $mapper  = new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Mapper', $mapperParams);

        $fillerParams = [
            'key_specification' => [$this, 'getOldDataFillerKey'],
            'backend' => new Repeater(
                [
                    'key_attribute' => 'sku'
                ]
            )
        ];
        $filler = new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Filler', $fillerParams);

        return [$mapper, $filler];
    }

    public function getOldDataFillerKey($item)
    {
        return [$item['new']['sku']];
    }

    public function mapToOldNewFormat($data)
    {
        $result = [
            'new' => $data,
            'old' => []
        ];

        return $result;
    }

    public function differ()
    {
        $params = [
            'type' => $this->getType()
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Differ', $params);
    }

    public function createFilter()
    {
        $mapperParams = [
            "fields" => [$this, "filterItemsToCreate"]
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Mapper', $mapperParams);
    }

    public function filterItemsToCreate($data)
    {
        if (isset($data['sku']['-']) && !empty($data['sku']['-'])) {
            return [];
        }

        return $data;
    }

    public function updateFilter()
    {
        $mapperParams = [
            "fields" => [$this, "filterItemsToUpdate"]
        ];
        return new ProcessorFactory('Webbhuset\Bifrost\Utils\Processor\Mapper', $mapperParams);
    }

    public function filterItemsToUpdate($data)
    {
        if (isset($data['sku']['-']) && !empty($data['sku']['-'])) {
            return $data;
        }

        return [];
    }

    public function varcharMapper()
    {
        $params = [
            "attributes" => [
                'name' => 1
            ],
        ];
        $class   = 'Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest\EntityToRowProcessor';
        return new ProcessorFactory($class, $params);
    }

    public function varcharInserter1()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'varchar1']);
    }
    public function varcharInserter2()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'varchar2']);
    }

    public function intMapper()
    {
        $params = [
            "attributes" => [
                'status' => 2
            ],
        ];
        $class   = 'Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest\EntityToRowProcessor';
        return new ProcessorFactory($class, $params);
    }

    public function intInserter1()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'int1']);
    }
    public function intInserter2()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'int2']);
    }

    public function decimalMapper()
    {
        $params = [
            "attributes" => [
                'price' => 3
            ],
        ];
        $class   = 'Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest\EntityToRowProcessor';
        return new ProcessorFactory($class, $params);
    }

    public function decimalInserter1()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'decimal1']);
    }
    public function decimalInserter2()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'decimal2']);
    }

    public function textMapper()
    {
        $params = [
            "attributes" => [
                'description' => 4
            ],
        ];
        $class   = 'Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest\EntityToRowProcessor';
        return new ProcessorFactory($class, $params);
    }

    public function textInserter1()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'text1']);
    }
    public function textInserter2()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'text2']);
    }

    public function datetimeMapper()
    {
        $params = [
            "attributes" => [
                'news_from_date' => 5
            ],
        ];
        $class   = 'Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest\EntityToRowProcessor';
        return new ProcessorFactory($class, $params);
    }

    public function datetimeInserter1()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'datetime1']);
    }
    public function datetimeInserter2()
    {
        return new WriterFactory('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', ['id' => 'datetime2']);
    }
}
