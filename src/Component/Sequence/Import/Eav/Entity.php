<?php

namespace Webbhuset\Bifrost\Component\Sequence\Import\Eav;

use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Component\Action;
use Webbhuset\Bifrost\Component\Db;
use Webbhuset\Bifrost\Component\Dev;
use Webbhuset\Bifrost\Component\Flow;
use Webbhuset\Bifrost\Component\Transform;
use Webbhuset\Bifrost\Helper;

class Entity implements ComponentInterface
{
    protected $component;

    public function __construct(array $config)
    {
        $default = [
            'idField'               => 'entity_id',
            'setField'              => 'attribute_set_id',
            'updateOnly'            => false,
            'attributesToUpdate'    => [],
            'batchSize'             => 200,
        ];

        $config = array_merge($default, $config);

        $this->component = $this->createComponent($config);
    }

    protected function createComponent($config)
    {
        $updateOnly         = $config['updateOnly'];
        $idField            = $config['idField'];
        $setField           = $config['setField'];
        $entityTable        = $config['entityTable'];
        $attributesToUpdate = $config['attributesToUpdate'];
        $attributesByTable  = $config['attributesByTable'];
        $dimensions         = $config['dimensions'];
        $attributeSets      = $config['attributeSets'];

        return new Flow\Multiplex(
            function($item) use ($idField) {
                return isset($item[$idField]) ? 'update' : 'create';
            },
            [
                'create' => $this->createEntities($config),
                'update' => $this->updateEntities($config),
            ]
        );
    }

    public function process($items, $finalize = true)
    {
        return $this->component->process($items, $finalize);
    }


    protected function createEntities($config)
    {
        $updateOnly = $config['updateOnly'];
        $batchSize  = $config['batchSize'];

        if ($updateOnly) {
            return [
                new Action\Event('notFound'),
            ];
        } else {
            return [
                $this->insertNewEntities($batchSize),
                new Action\Event('created'),
                $this->fillAttributeNull($config),
                $this->insertAttributes($config),
            ];
        }
    }

    protected function updateEntities($config)
    {
        $idField            = $config['idField'];
        $setField           = $config['setField'];
        $entityTable        = $config['entityTable'];
        $attributesToUpdate = $config['attributesToUpdate'];
        $attributesByTable  = $config['attributesByTable'];
        $dimensions         = $config['dimensions'];
        $attributeSets      = $config['attributeSets'];

        return [
            $this->getOldAttributeData($config),
            $this->diffNewAndOldData(),
            new Transform\Expand(function($items) {
                foreach ($items['new'] as $idx => $item) {
                    yield [
                        'new'   => $items['new'][$idx],
                        'old'   => $items['old'][$idx],
                        'diff'  => $items['diff'][$idx],
                    ];
                }
            }),
            $this->filterAttributesToUpdate($attributesToUpdate),
            $this->filterAttributesBySet($setField, $attributeSets),
            new Flow\Multiplex(
                function($item) {
                    return $item['diff'] ? 'updated' : 'skipped';
                },
                [
                    'skipped' => new Action\Event('skipped'),
                    'updated' => new Flow\Pipeline([
                        new Action\Event('updated'),
                        new Flow\Fork([
                            new Flow\Pipeline([
                                $this->mapDiff($idField),
                                $this->insertAttributes($config)
                            ]),
                            new Flow\Pipeline($this->updateUpdatedAt($config)),
                        ])
                    ]),
                ]
            ),
        ];
    }

    protected function insertNewEntities($batchSize)
    {
        return [
            new Transform\Group($batchSize),
            new Transform\Merge(
                new Action\SideEffect('insertNewEntities')
            ),
            new Transform\Expand(function($items) {
                foreach ($items as $item) {
                    yield $item;
                }
            }),
        ];
    }

    protected function fillAttributeNull($config)
    {
        $sets               = $config['attributeSets'];
        $idField            = $config['idField'];
        $setField           = $config['setField'];

        return [
            new Transform\Map(function($item) use ($sets, $setField) {
                $setId  = $item[$setField];
                $set    = $sets[$setId];

                foreach ($set as $code => $attribute) {
                    if (!array_key_exists($code, $item)) {
                        $item[$code] = [null];
                    }
                }

                return $item;
            }),
        ];
    }

    protected function getOldAttributeData($config)
    {
        $idField            = $config['idField'];
        $batchSize          = $config['batchSize'];
        $updateAttributes   = $config['attributesToUpdate'];
        return [
            new Transform\Group($batchSize),
            new Transform\Map(function($items) {
                return ['new' => $items];
            }),
            new Transform\Merge(
                new Flow\Pipeline([
                    new Transform\Map(function($items) use ($idField) {
                        $ids = [];
                        foreach ($items['new'] as $item) {
                            $ids[] = $item[$idField];
                        }

                        return $ids;
                    }),
                    new Action\SideEffect('getOldAttributeData'),
                    new Transform\Map(function($items) {
                        return ['old' => $items];
                    }),
                ])
            ),
        ];
    }

    protected function diffNewAndOldData()
    {
        return new Transform\Map(function($items) {
            foreach ($items['new'] as $idx => $item) {
                $new = $items['new'][$idx];
                $old = $items['old'][$idx];
                $items['diff'][$idx] = Helper\ArrayHelper\Tree::diffRecursive($new, $old);
            }

            return $items;
        });
    }

    protected function filterAttributesToUpdate($attributes)
    {
        return new Transform\Map(function($item) use ($attributes) {
            foreach ($item['diff'] as $attribute => $diff) {
                if (!in_array($attribute, $attributes)) {
                    unset($item['diff'][$attribute]);
                }
            }
            return $item;
        });
    }

    protected function filterAttributesBySet($setField, $attributeSets)
    {
        return new Transform\Map(function($item) use ($setField, $attributeSets) {
            $attributes = $attributeSets[$item['new'][$setField]];
            foreach ($item['diff'] as $attribute => $diff) {
                if (!array_key_exists($attribute, $attributes)) {
                    unset($item['diff'][$attribute]);
                }
            }
            return $item;
        });
    }

    protected function insertAttributes($config)
    {
        $idField            = $config['idField'];
        $entityTable        = $config['entityTable'];
        $attributesByTable  = $config['attributesByTable'];
        $dimensions         = $config['dimensions'];
        $batchSize          = $config['batchSize'];
        $codeToIdMapper     = $config['codeToIdMapper'];
        $staticColumns      = $config['staticColumns'];
        $valueTableColumns  = $config['valueTableColumns'];

        $pipelines = [];

        foreach ($attributesByTable as $table => $attributes) {
            $pipelineArray = [
                $this->filterAttributesByTable($idField, $attributes),
            ];
            if ($table == $entityTable) {
                $pipelineArray = array_merge($pipelineArray, [
                    new Action\SideEffect('updateEntityRow', $table),
                ]);
            } else {
                $pipelineArray = array_merge($pipelineArray, [
                    $this->mapToAttributeIds($idField, $codeToIdMapper),
                    new Db\TreeToTable($valueTableColumns, $dimensions, $staticColumns),
                    new Transform\Group($batchSize),
                    new Action\SideEffect('insertAttributeRows', $table),
                ]);
            }

            $pipelines[] = new Flow\Pipeline($pipelineArray);
        }

        return [
            new Flow\Fork($pipelines)
        ];
    }

    protected function mapDiff($idField)
    {
        return new Transform\Map(function($item) use ($idField) {
            $newItem = [
                $idField => $item['new'][$idField]
            ];

            foreach ($item['diff'] as $attribute => $value) {
                $newItem[$attribute] = $value;
            }

            return $newItem;
        });
    }

    protected function filterAttributesByTable($idField, $attributes)
    {
        return [
            new Transform\Map(function($item) use ($idField, $attributes) {
                if (!isset($item[$idField])) {
                    return [];
                }
                $newItem = [
                    $idField => $item[$idField]
                ];

                foreach ($attributes as $attribute) {
                    if (array_key_exists($attribute, $item)) {
                        $newItem[$attribute] = $item[$attribute];
                    }
                }

                return $newItem;
            }),
            new Transform\Filter(function($item) {
                return count($item) > 1;
            })
        ];
    }

    protected function mapToAttributeIds($idField, $mapper)
    {
        return new Transform\Map(function($item) use ($idField, $mapper) {
            $id = $item[$idField];
            unset($item[$idField]);

            return [$id => $mapper->map($item)];
        });
    }

    protected function updateUpdatedAt($config)
    {
        $idField            = $config['idField'];
        $batchSize          = $config['batchSize'];

        return [
            new Transform\Map(function($item) use ($idField) {
                return $item['new'][$idField];
            }),
            new Transform\Group($batchSize),
            new Action\SideEffect('updateUpdatedAt'),
        ];
    }
}
