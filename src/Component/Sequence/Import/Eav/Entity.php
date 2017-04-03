<?php

/**
 * Pre Req:
 *      Attribute Set field must exist (attribute_set_id)
 *      Attribute Set id must exist in $sets
 */
namespace Webbhuset\Bifrost\Core\Component\Sequence\Import\Eav;

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Helper;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\Type\TypeConstructor AS T;

class Entity implements Component\ComponentInterface
{
    protected $pipeline;
    protected $config;

    public function __construct(array $config)
    {
        $default = [
            'idFieldName'   => 'entity_id',
            'setFieldName'  => 'attribute_set_id',
            'batchSize'     => 500,
            'defaultScope'  => 0,
            'skipCreate'    => false,
            'updateAttributes' => [],
        ];
        $config = array_merge($default, $config);

        $required = ['required' => true];

        $stringOrIntUnion = T::Union([
            'required'  => true,
            'types'     => [T::String($required), T::Int($required)],
        ]);

        $configType = T::Struct([
            'fields' => [
                'attributesByType' => T::Map([
                    'key_type'      => $stringOrIntUnion,
                    'value_type'    => T::Set(['type' => $stringOrIntUnion, 'required' => true]),
                ]),
                'attributeSetsByName' => T::Map([
                    'key_type'      => $stringOrIntUnion,
                    'value_type'    => T::Set(['type' => $stringOrIntUnion, 'required' => true]),
                ]),
                'valueTableConfig' => T::Struct([
                    'fields' => [
                        'columns'       => T::Set(['type' => T::String(), 'required' => true]),
                        'dimensions'    => T::Set(['type' => T::String(), 'required' => true]),
                        'static'        => T::Map([
                            'key_type'      => T::String(),
                            'value_type'    => T::Any(),
                        ])
                    ],
                ]),
                'idFieldName'  => T::String($required),
                'setFieldName' => T::String($required),
                'batchSize'    => T::Int(['min_value' => 2, 'required' => true]),
                'defaultScope' => T::Union(['types' => [T::String(), T::Int()]]),
                'skipCreate'   => T::Bool(),
                'updateAttributes' => T::Set(['type' => T::String()]),
            ],
        ]);
        $errors = $configType->getErrors($config);

        if ($errors !== false) {
            throw new Type\TypeException("Constructor param is not correct.", 0, null, $errors);
        }

        $this->config = $config;
    }

    public function process($items, $finalize = true)
    {
        if (!$this->pipeline) {
            $this->pipeline = $this->makePipeline($this->config);
        }

        return $this->pipeline->process($items, $finalize);
    }

    protected function makePipeline($config)
    {
        $batchSize = $config['batchSize'];
        $skipCreate = $config['skipCreate'];

        return new Component\Flow\Pipeline([
            //$this->batchAndMergeResult($batchSize, 'getEntityIds'),
            new Component\Flow\Fork([
                $skipCreate ? false : $this->createNewEntities($config),
                $this->updateExistingEntities($config),
            ]),
        ]);
    }

    public function batchAndMergeResult($batchSize, $method)
    {
        return new Component\Transform\Merge(
            new Component\Flow\Pipeline([
                new Component\Transform\Group($batchSize),
                new Component\Action\SideEffect($method),
                new Component\Transform\Expand(function($entities) {
                    foreach ($entities as $entity) {
                        if ($entity['attribute_set_id']) {
                            $entity['attribute_set_id'] = (int)$entity['attribute_set_id'];
                        }
                        yield $entity;
                    }
                }),
            ])
        );
    }

    protected function createNewEntities($config)
    {
        $attributesByType = $config['attributesByType'];
        $sets             = $config['attributeSetsByName'];
        $idFieldName      = $config['idFieldName'];
        $setFieldName     = $config['setFieldName'];
        $batchSize        = $config['batchSize'];
        $defaultScope     = $config['defaultScope'];

        return new Component\Flow\Pipeline([
            $this->filterByColumnValue($idFieldName, null),
            $this->batchAndMergeResult($batchSize, 'createEntities'),
            new Component\Action\Event('created'),
            $this->fillAttributeNullValues($sets, $setFieldName, $defaultScope),
            $this->handleAttributeValues($config),
        ]);
    }

    protected function updateExistingEntities($config)
    {
        $attributesByType = $config['attributesByType'];
        $sets             = $config['attributeSetsByName'];
        $idFieldName      = $config['idFieldName'];
        $setFieldName     = $config['setFieldName'];
        $batchSize        = $config['batchSize'];
        $defaultScope     = $config['defaultScope'];

        return new Component\Flow\Pipeline([
            $this->filterByColumnValue($idFieldName, true),
            $this->handleAttributeValues($config, true),
        ]);
    }

    protected function filterByColumnValue($field, $value)
    {
        return new Component\Transform\Filter(function($item) use ($field, $value) {
            return $item[$field] == $value;
        });
    }

    protected function handleAttributeValues($config, $compareWithOldValues = false)
    {
        $attributesByType = $config['attributesByType'];
        $sets             = $config['attributeSetsByName'];
        $valueTableConfig = $config['valueTableConfig'];
        $idFieldName      = $config['idFieldName'];
        $setFieldName     = $config['setFieldName'];
        $batchSize        = $config['batchSize'];

        $pipelines = [];

        foreach ($attributesByType as $type => $attributesForType) {
            if ($type == 'static') {
                continue;
            }

            if (count($attributesForType) === 0) {
                continue;
            }

            $filteredSets = $this->intersectSets($sets, $attributesForType);

            if (!array_filter($filteredSets)) {
                continue;
            }

            $pipelines[] = new Component\Flow\Pipeline([
                $this->prepareTree($idFieldName, $setFieldName, $filteredSets),
                $compareWithOldValues ? $this->compareWithOldValues($config, $attributesForType, $type) : false,
                $this->dropEmptyItems(),
                $compareWithOldValues ? new Component\Action\Event('updated') : false,
                $this->treeToTable($valueTableConfig),
                new Component\Transform\Group($batchSize * 3),
                new Component\Action\SideEffect('insertAttributeValues', $type),
            ]);
        }

        switch (true) {
            case count($pipelines) > 1:  return new Component\Flow\Fork($pipelines);
            case count($pipelines) == 1: return reset($pipelines);
            case count($pipelines) < 1:  return null;
        }
    }

    protected function compareWithOldValues($config, $attributes, $type)
    {
        $sets             = $config['attributeSetsByName'];
        $valueTableConfig = $config['valueTableConfig'];
        $idFieldName      = $config['idFieldName'];
        $setFieldName     = $config['setFieldName'];
        $batchSize        = $config['batchSize'];

        return new Component\Flow\Pipeline([
            new Component\Transform\Group($batchSize, true),
            new Component\Transform\Map(function($item) {
                $item = [
                    'new' => $item,
                ];

                return $item;
            }),
            $this->fetchAttributeValues($type, $attributes, $config),
            $this->diffAttributeValues(),
        ]);
    }

    protected function fetchAttributeValues($type, $attributes, $config)
    {
        $valueTableConfig = $config['valueTableConfig'];
        $batchSize        = $config['batchSize'];
        $dimensions       = $valueTableConfig['dimensions'];

        return new Component\Transform\Merge(
            new Component\Flow\Pipeline([
                new Component\Transform\Map(function($item) {
                    return array_keys($item['new']);
                }),
                new Component\Action\SideEffect('fetchAttributeValues', $type, $attributes),
                new Component\Db\TableToTree($dimensions),
                new Component\Transform\Map(function($item) {
                    return ['old' => $item];
                }),
            ])
        );
    }

    protected function diffAttributeValues()
    {
        return new Component\Transform\Map(function ($item) {
            $new = $item['new'];
            $old = $item['old'];
            $insertTree = Helper\ArrayHelper\Tree::diffRecursive($new, $old);

            return $insertTree;
        });
    }

    protected function fillAttributeNullValues($sets, $setField, $defaultScope = 0)
    {
        $usesScope      = $defaultScope !== null;
        $defaultScope   = $usesScope
                        ? [$defaultScope => null]
                        : null;

        return new Component\Transform\Map(function($item) use ($sets, $setField, $defaultScope) {
            $setName    = $item[$setField];
            $set        = $sets[$setName];

            return array_replace(array_fill_keys($set, $defaultScope), $item);
        });
    }

    protected function prepareTree($rootField, $setField, $sets)
    {
        return new Component\Transform\Map(function($item) use ($rootField, $setField, $sets) {
            $rootValue          = $item[$rootField];
            $setName            = $item[$setField];
            $attributes         = $sets[$setName];
            $filteredItem       = array_intersect_key($item, $attributes);

            if (empty($filteredItem)) {
                return $filteredItem;
            }

            $treeItem = [$rootValue => $filteredItem];

            return $treeItem;
        });
    }

    protected function intersectSets($sets, $attributes)
    {
        $intersectedSets = [];

        foreach ($sets as $name => $attributesInSet) {
            $validAttributes        = array_intersect($attributes, $attributesInSet);
            $intersectedSets[$name] = array_flip(array_values($validAttributes));
        }

        return $intersectedSets;
    }

    protected function dropEmptyItems()
    {
        return new Component\Transform\Filter(function($item) {
            return !empty($item);
        });
    }

    protected function treeToTable($config)
    {
        $columns    = $config['columns'];
        $dimensions = $config['dimensions'];
        $static     = $config['static'];

        return new Component\Db\TreeToTable($columns, $dimensions, $static);
    }
}
