<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\Helper;

class Webbhuset_Bifrost_Model_Service_Import_Eav_Attribute_Option
{
    public function createSequence(Mage_Eav_Model_Entity_Type $type)
    {
        $attributes = $this->getAttributesWithOptions($type->getAttributeCollection());

        return [
            $this->getComponentEntityValidator($attributes),
            $this->getComponentMapAttributes($attributes),
            $this->getComponentTableUpdater(),
            $this->createMonad($attributes),
        ];
    }

    public function createEntityFileReducer(Mage_Eav_Model_Entity_Type $type)
    {
        $attributes = $this->getAttributesWithOptions($type->getAttributeCollection());

        return [
            $this->getComponentOptionsReducer($attributes),
            $this->getComponentMapTree($attributes),
        ];
    }

    public function getAttributesWithOptions($allAttributes)
    {
        $attributes = [];
        foreach ($allAttributes as $attribute) {
            $code   = $attribute->getAttributeCode();
            $id     = $attribute->getId();
            $type   = $attribute->getBackendType();
            $source = $attribute->getSourceModel();

            if (!$source) {
                continue;
            }

            $sourceModel = $attribute->getSource();

            if (!$sourceModel instanceof Mage_Eav_Model_Entity_Attribute_Source_Table) {
                continue;
            }
            $attributes[$code] = $attribute;
        }

        return $attributes;
    }

    protected function getComponentEntityValidator()
    {
        $type = new Type\StructType([
            'fields' => [
                'attribute'     => new Type\StringType(['required' => true]),
                'sort_order'    => new Type\IntType(['required' => true]),
                'label'         => new Type\HashmapType([
                    'key_type'      => new Type\IntType(['required' => true]),
                    'value_type'    => new Type\StringType(['required' => true]),
                ]),
            ],
        ]);

        return new Component\Validate\Entity($type);
    }

    protected function getComponentTableUpdater()
    {
        $config = [
            'columns' => [
                'option_id',
                'attribute_id',
                'sort_order',
                'label',

            ],
            'primaryKey'        => 'option_id',
            'updateColumns'     => [
                'sort_order',
            ],
            'batchSize'         => 200,
        ];
        return new Component\Sequence\Import\Table\Flat($config);
    }

    protected function getComponentOptionsReducer($attributes)
    {
        $codes = array_keys($attributes);

        return new Component\Transform\Reduce(function($carry, $item) use ($attributes) {
            foreach ($attributes as $code => $attribute) {
                if (!isset($item[$code])) {
                    continue;
                }
                $value  = trim($item[$code]);
                $key    = mb_strtoupper($value);

                if (isset($item[$code][$key])) {
                    continue;
                }

                $carry[$code][$key] = $value;
            }

            return $carry;
        }, array_fill_keys($codes, []));
    }

    protected function getComponentMapTree($attributes)
    {
        return new Component\Transform\Expand(function($data) use ($attributes) {
            foreach ($data as $attributeCode => $options) {
                foreach ($options as $key => $labels) {
                    $item = [
                        'attribute'     => $attributeCode,
                        'sort_order'    => 0,
                        'label'         => [$labels],
                    ];
                    yield $item;
                }
            }
        });
    }

    protected function getComponentMapAttributes($attributes)
    {
        return new Component\Transform\Map(function($data) use ($attributes) {
            $code   = $data['attribute'];
            $id     = $attributes[$code]->getId();
            $label  = $data['label'];
            $key    = mb_strtoupper($label[0]);
            return [
                'attribute_id'  => $id,
                'sort_order'    => $data['sort_order'],
                'key'           => $key,
                'label'         => $label,
            ];
        });
    }

    protected function createMonad($attributes)
    {
        $resource   = Mage::getSingleton('core/resource');
        $adapter    = $resource->getConnection('core_write');

        $config['adapter']            = $adapter;
        $config['optionTable']        = $resource->getTableName('eav/attribute_option');
        $config['optionValueTable']   = $resource->getTableName('eav/attribute_option_value');

        $qHeader = 'SELECT '
                 . 'MAX(`o`.`option_id`) AS `option_id`,'
                 . 'MAX(`o`.`attribute_id`) AS `attribute_id`,'
                 . 'MAX(`o`.`sort_order`) AS `sort_order`,'
                 . '`v`.`value` FROM ';
        $qFooter = "LEFT JOIN `eav_attribute_option_value` AS `v` ON `v`.`store_id` = 0 "
                 .     "AND UPPER(`v`.`value`) = `_key`.`key`\n"
                 . "LEFT JOIN `eav_attribute_option` AS `o` ON `o`.`attribute_id` = `_key`.`attribute_id` "
                 .     "AND `o`.`option_id` = `v`.`option_id`\n"
                 . "GROUP BY `_key`.`pos`";

        $queryBuilder = new Helper\Db\QueryBuilder\StaticUnion(['attribute_id', 'key'], $adapter, $qHeader, $qFooter);

        $config['queryBuilder'] = $queryBuilder;
        $actionHandler          = Mage::getModel('whbifrost/resource_import_eav_attribute_optionActions', $config);

        return new Component\Monad\Standard($actionHandler);
    }
}
