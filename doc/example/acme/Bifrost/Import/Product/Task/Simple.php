<?php

namespace Acme\Bifrost\Import\Product\Task;

class Simple extends Webbhuset\Bifrost\Job\Task\Factory;
{
    public function createTask()
    {
        $fields = $this->getFields();

        $reader = new Webbhuset\Bifrost\Utils\Reader\Csv;
        $mapper = new Webbhuset\Bifrost\Job\Task\Source\Mapper\Simple($reader, $fields);

        $validator = new Webbhuset\Bifrost\Job\Task\Validator\Default($fields);
        $destination = new Webbhuset\Bifrost\Job\Task\Destination\Batch(
            'backend' => new Webbhuset\Bifrost\MageOne\Batch\Product;
        );

        $logger = new Webbhuset\Bifrost\MageOne\Logger;

        $task = new Webbhuset\Bifrost\Job\Task(
            'simple',
            [
                'source'        => $mapper,
                'destination'   => $destination,
                'validator'     => $validator,
                'logger'        => $logger,
            ]
        );

        return $task;
    }

    public function getFields()
    {
        return [
            'sku'           => [
                'map' => 'ArticleNo',
                'type' => new Webbhuset\Bifrost\Utils\Type\Sku(['is_required' => true]),
            ],
            'name'          => [
                'map' => 'Title',
                'type' => new Webbhuset\Bifrost\Utils\Type\String([
                    'min_length'    => 4,
                    'regex_ok'      => [
                        '/^[\w\d\s]+$/' => 'Only letters, numbers and spaces are allowed.'
                    ],
                    'regex_fail'    => [
                        '/^\s+/' => 'Field can not start with whitespace.'
                        '/\s+$/' => 'Field can not end with whitespace.'
                    ],
                ]),
            ],
            'type_id'      => [
                'type' => new Webbhuset\Bifrost\Utils\Type\Int,
                'default' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            ],
            'price'         => [
                'map' => 'Price',
                'type' => new Webbhuset\Bifrost\Utils\Type\Price,
                'default' => 0,
            ],
            'status'         => [
                'map' => 'Status',
                'type' => new Webbhuset\Bifrost\Utils\Type\Boolean,
                'default' => true,
            ],
            'website_ids'   => [
                'map' => [$this, 'mapWebsiteIds'],
                'type' => new Webbhuset\Bifrost\Utils\Type\IdsList,
            ],
        ];
    }

    public function mapWebsiteIds($data)
    {
        return array_keys(Mage::app()->getWebsites());
    }
}
