<?php

namespace Acme\Bifrost\Import\Product\Task;

class Configurable extends Simple
{
    public function createTask()
    {
        $fields = $this->getFields();
        $reader = new Webbhuset\Bifrost\Utils\Reader\Csv;

        $simpleMapper = new Webbhuset\Bifrost\Job\Task\Source\Mapper\Simple(
            [
                'parent' => $reader,
                'fields' => $fields
            ]
        );
        $configurableMapper = new Webbhuset\Bifrost\Job\Task\Source\Mapper\Configurable(
            [
                'parent' => $simpleMapper,
                'fields' => $fields
            ]
        );

        $type   = Webbhuset\Bifrost\MageOne\TypeFactories\ProductTypeFactory::createType();

        $destination = new Webbhuset\Bifrost\Job\Task\Destination\Batch(
            [
                'backend' => new Webbhuset\Bifrost\MageOne\Batch\Product\Configurable
            ]
        );

        $logger = new Webbhuset\Bifrost\MageOne\Logger;

        $task = new Webbhuset\Bifrost\Job\Task(
            'simple',
            [
                'source'        => $mapper,
                'destination'   => $destination,
                'type'          => $type,
                'logger'        => $logger,
            ]
        );

        return $task;
    }

    public function getFields()
    {
        $fields = parent::getFields();
        $fields['type_id'] = [
            'type' => new Webbhuset\Bifrost\Utils\Type\Int,
            'default' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        ];

        return $fields;
    }
}
