<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Helper\Eav;

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Component\Sequence\Import\Eav\Entity\ActionsInterface;
use Webbhuset\Bifrost\Core\Data\Eav;

class EntityTypeCreatorTest
{
    public static function createFromAttributesTest($test)
    {
        $attributes     = self::getTestAttributes();
        $ns = 'Webbhuset\\Bifrost\\Core\\Type\\';

        $test
            ->testThatArgs($attributes)
            ->returnsInstanceOf($ns.'StructType');
    }

    protected static function getTestAttributes()
    {
        $globalAttributes = [
            'sku'               => 'static',
            'small_image'       => 'varchar',
            'image'             => 'varchar',
            'thumbnail'         => 'varchar',
            'status'            => 'int',
            'weight'            => 'decimal',
        ];
        $langAttributes = [
            'name'              => 'varchar',
            'description'       => 'text',
            'short_description' => 'text',
        ];

        $currencyAttributes = [
            'price'             => 'decimal',
        ];


        $scopes = [
            'global' => new Eav\Attribute\Scope([]),
            'lang' => new Eav\Attribute\Scope([
                'en' => [0],
                'sv' => [1, 3],
                'fr' => [2, 4],
            ]),
            'currency' => new Eav\Attribute\Scope([
                'USD' => [0],
                'SEK' => [1, 3],
                'EUR' => [2, 4],
            ]),
        ];

        $id = 1;
        $makeAttributes = function($data, $scope) use (&$id) {
            $attributes = [];
            foreach ($data as $code => $backendType) {
                $attribute = new Eav\Attribute([
                    'id'            => $id,
                    'code'          => $code,
                    'backendType'   => $backendType,
                    'scope'         => $scope,
                    'shouldUpdate'  => in_array($code, ['name', 'description', 'price']),
                    'table'         => $backendType,
                ]);
                $attributes[$code] = $attribute;
                $id += 1;
            }
            return $attributes;
        };
        $attributes = $makeAttributes($globalAttributes, $scopes['global']);
        $attributes += $makeAttributes($langAttributes, $scopes['lang']);
        $attributes += $makeAttributes($currencyAttributes, $scopes['currency']);

        return $attributes;
    }

    protected static function getItems()
    {
        return [
            [
                'sku'                 => '1000001-10000-4868',
                'attribute_set_name'  => 'Default',
                'name'                => [
                    'en' => 'Name EN',
                    'sv' => 'Name SE',
                ],
                'description'         => 'da76d8b10a743a5e82f27c75a9449c22da76d8b10a743a5e82f2',
                'short_description'   => '0b5b40681f446eff53783',
                'price'               => [
                    'USD' => 9.95,
                    'SEK' => 99.0,
                ],
                'weight'              => 14560.686813186812,
                'image'               => '3dda8f',
                'small_image'         => 'e78d3b',
                'thumbnail'           => '0a265',
            ],
            [
                'sku'                 => '1000002-10000-0134',
                'attribute_set_name'  => 'Other',
                'name'                => [
                    'en' => 'Name EN',
                    'sv' => 'Name SE',
                ],
                'short_description'   => 'ed5a5c5c0cdea01677f70a8dfff28710ed5a5c5c',
                'price'               => [
                    'USD' => 9.95,
                    'SEK' => 99.0,
                ],
                'weight'              => 26066.383641525357,
                'small_image'         => 'Fisk',
            ],
            [
                'sku'                 => '1000003-10000-7244',
                'attribute_set_name'  => 'Default',
                'name'                => [
                    'en' => 'Name EN',
                    'sv' => 'Name SE',
                    'fr' => 'Name FR',
                ],
                'description'         => '97640799764d85e0aa3aaff973353dc760870799764d85e0aa3aaff973353dc76087',
                'short_description'   => '99f5e54c283f72fa5e68b82ec05e400499',
                'price'               => [
                    'USD' => 9.95,
                    'SEK' => 99.0,
                ],
                'weight'              => 42723.23406595979,
                'image'               => 'b2b21c233',
                'small_image'         => 'a7644',
                'thumbnail'           => '31c3d7',
             ],
        ];
    }
}

class TestActions implements ActionsInterface
{
    public $fakeDb = [
        'entity' => [],
    ];

    public function getEntityIds($entities)
    {
        $result = [];
        foreach ($entities as $entity) {
            $key = $entity['sku'];
            if (isset($this->fakeDb['entity'][$key])) {
                $id = $this->fakeDb['entity'][$key]['entity_id'];
                $result[] = ['entity_id' => $id];
            } else {
                $result[] = ['entity_id' => null];
            }
        }
        return $result;
    }
    public function createEntities($entities)
    {
        $result = [];
        foreach ($entities as $entity) {
            $key = $entity['sku'];
            $id  = count($this->fakeDb['entity']) + 1;
            $this->fakeDb['entity'][$key] = [
                'entity_id' => $id,
                'sku'       => $key,
            ];
        }
    }
    public function insertAttributeValues($rows, $type)
    {
        if (!isset($this->fakeDb[$type])) {
            $this->fakeDb[$type] = [];
        }

        foreach ($rows as $row) {
            $entityId       = $row['entity_id'];
            $attributeCode  = $row['attribute_code'];
            $scope          = $row['scope'];
            $key            = implode('/', [$entityId, $attributeCode]);
            if (!isset($this->fakeDb[$type][$key])) {
                $this->fakeDb[$type][$key] = [];
            }
            $this->fakeDb[$type][$key][$scope] = $row;
        }
    }
    public function fetchAttributeValues($entityIds, $type, $attributes)
    {
        $values = [];
        foreach ($entityIds as $entityId) {
            $values[$entityId] = [];
            foreach ($attributes as $code) {
                $key = implode('/', [$entityId, $code]);

                if (isset($this->fakeDb[$type][$key])) {
                    $value = $this->fakeDb[$type][$key];
                    foreach ($value as $scope => $v) {
                        $values[$entityId][$code][$scope] = $v['value'];
                    }
                }
            }
        }
        return $values;
    }
}