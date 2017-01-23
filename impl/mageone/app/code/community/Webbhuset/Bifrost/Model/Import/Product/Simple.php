<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Type;

class Webbhuset_Bifrost_Model_Import_Product_Simple
{
    public function create($config = [])
    {
        $config = array_merge([
            'updateAttributes'      => [],
            'keys'                  => ['sku'],
            'afterCreateCallback'   => $this->getAfterCreateCallback(),
            'types'                 => $this->getAttributeTypes(),
        ], $config);

        $type = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
        $type->getAttributeCollection()
            ->addFieldToFilter(
                ['apply_to'],
                [
                    [
                        ['like' => '%simple%'],
                        ['null' => true]
                    ]
                ]
            );

        $import = Mage::getModel('whbifrost/service_import_eav_entity')->create($type, $config);

        $defaults = [
            'type_id'           => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            'entity_type_id'    => (int)$type->getId(),
            'has_options'       => 0,
            'required_options'  => 0,
        ];

        $pipeline = [
            new Core\Component\Transform\Map(function ($item) use ($defaults) {
                $item = array_merge($defaults, $item);

                return $item;
            }),
        ];

        return array_merge($pipeline, $import);
    }

    protected function getAttributeTypes()
    {
        return [
            'sku' => new Type\StringType([
                'min_length' => 1,
                'max_length' => 64,
                'not_matches' => [
                    '/(^\s+\S)|(\S\s+$)/' => 'Value contains trailing whitespace',
                ],
            ]),
        ];
    }

    protected function getAfterCreateCallback()
    {
        $insertStockItem = $this->createStockItemFunction();

        return $insertStockItem;
    }

    protected function createStockItemFunction()
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');

        $stockTable = $resource->getTableName('cataloginventory/stock_item');
        $productTable = $resource->getTableName('catalog/product');
        $columns = array_keys($adapter->describeTable($stockTable));

        $columns = array_fill_keys($columns, new Zend_Db_Expr('NULL'));

        $columns['product_id']      = 'e.entity_id';
        $columns['stock_id']        = new Zend_Db_Expr('1');
        $columns['is_in_stock']     = new Zend_Db_Expr('1');
        $columns['qty']             = new Zend_Db_Expr('10');
        $columns['min_sale_qty']    = new Zend_Db_Expr(
            Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_SALE_QTY)
        );

        $columns = array_merge($columns, array_fill_keys([
                'use_config_min_qty',
                'use_config_backorders',
                'use_config_min_sale_qty',
                'use_config_max_sale_qty',
                'use_config_notify_stock_qty',
                'use_config_manage_stock',
                'use_config_qty_increments',
                'use_config_enable_qty_inc',
            ],
            new Zend_Db_Expr(1))
        );

        $select = $adapter->select()
            ->from(['e' => $productTable], [])
            ->joinLeft(
                ['i' => $stockTable],
                'i.product_id = e.entity_id',
                []
            )
            ->where('i.item_id IS NULL');

        $select->columns($columns);
        $query = $adapter->insertFromSelect($select, $stockTable);

        return function($rows, $adapter, $entityTable) use ($query) {
            $lastId = $adapter->lastInsertId($entityTable);
            $adapter->query($query . ' AND e.entity_id >= '. (int)$lastId);
        };
    }
}
