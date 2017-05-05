<?php

namespace Webbhuset\Bifrost\Component\Sequence\Import;

use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Component\Action;
use Webbhuset\Bifrost\Component\Db;
use Webbhuset\Bifrost\Component\Dev;
use Webbhuset\Bifrost\Component\Flow;
use Webbhuset\Bifrost\Component\Transform;
use Webbhuset\Bifrost\Helper;
use Webbhuset\Bifrost\Type;

class Entity implements ComponentInterface
{
    protected $component;

    public function __construct(array $config)
    {
        $default = [
            'idField'               => 'entity_id',
            'setField'              => null,
            'updateOnly'            => false,
            'attributesToUpdate'    => [],
            'attributeSets'         => [],
            'batchSize'             => 200,
        ];

        $config = array_replace($default, $config);

        $this->component = $this->createComponent($config);
    }

    protected function createComponent($config)
    {
        $idField = $config['idField'];

        return new Flow\Pipeline([
            new Flow\Multiplex(
                function($item) use ($idField) {
                    return isset($item[$idField]) ? 'update' : 'create';
                },
                [
                    'create' => new Flow\Pipeline($this->createEntities($config)),
                    'update' => new Flow\Pipeline($this->updateEntities($config)),
                ]
            ),
        ]);
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
                $this->filterEntityTableAttributes($config),
                $this->insertAttributes($config),
            ];
        }
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

    protected function filterEntityTableAttributes($config)
    {
        $idField            = $config['idField'];
        $entityTableName    = $config['entityTableName'];
        $attributes         = $config['attributesByTable'][$entityTableName];

        return [
            new Transform\Map(function($item) use ($idField, $attributes) {
                foreach ($item as $attribute => $value) {
                    if ($attribute != $idField && in_array($attribute, $attributes)) {
                        unset($item[$attribute]);
                    }
                }

                return $item;
            }),
        ];
    }

    protected function updateEntities($config)
    {
        $attributeData      = $config['attributeData'];
        $attributesToUpdate = $config['attributesToUpdate'];
        $setField           = $config['setField'];
        $attributeSets      = $config['attributeSets'];

        return [
            $this->getOldAttributeData($config),
            $this->diffNewAndOldData($attributeData),
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
                                $this->mapDiff($config),
                                $this->insertAttributes($config),
                            ]),
                            new Flow\Pipeline($this->updatedIds($config)),
                        ]),
                    ]),
                ]
            ),
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

    protected function diffNewAndOldData($attributeData)
    {
        return new Transform\Map(function($items) use ($attributeData) {
            foreach ($items['new'] as $idx => $item) {
                $new = $items['new'][$idx];
                $old = $items['old'][$idx];
                $items['diff'][$idx] = Helper\ArrayHelper\Tree::diffRecursive($new, $old);

                foreach ($items['new'][$idx] as $attribute => $value) {
                    if (!array_key_exists($attribute, $attributeData)
                        || !$attributeData[$attribute]->getTypeObject() instanceof Type\SetType
                    ) {
                        continue;
                    }
                    unset($items['diff'][$idx][$attribute]);

                    $newSet = $items['new'][$idx][$attribute];
                    $oldSet = $items['old'][$idx][$attribute];
                    $diff   = array_diff($newSet, $oldSet);
                    if ($diff) {
                        $items['diff'][$idx][$attribute] = $diff;
                    }
                }
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
        if ($setField === null) {
            return [];
        }

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

    protected function mapDiff($config)
    {
        $idField            = $config['idField'];
        $attributeData      = $config['attributeData'];
        $entityTable        = $config['entityTableName'];
        $attributesByTable  = $config['attributesByTable'];

        return new Transform\Map(function($item) use ($idField, $attributeData, $entityTable, $attributesByTable) {
            $diffItem = [
                $idField => $item['new'][$idField]
            ];

            foreach ($item['diff'] as $attribute => $value) {
                if ($attributeData[$attribute]->getTable() == $entityTable) {
                    foreach ($attributesByTable[$entityTable] as $entityAttribute) {
                        if (isset($item['old'][$entityAttribute])) {
                            $diffItem[$entityAttribute] = $item['old'][$entityAttribute];
                        }
                    }
                    break;
                }
            }

            foreach ($item['diff'] as $attribute => $value) {
                $diffItem[$attribute] = $value;
            }

            return $diffItem;
        });
    }

    protected function insertAttributes($config)
    {
        $idField            = $config['idField'];
        $entityTable        = $config['entityTableName'];
        $attributesByTable  = $config['attributesByTable'];
        $batchSize          = $config['batchSize'];
        $tables             = $config['tables'];

        $pipelines = [];

        foreach ($attributesByTable as $table => $attributes) {
            if ($table == $entityTable) {
                $tree = [];
            } else {
                $mapper         = $tables[$table]->getMapper();
                $columns        = $tables[$table]->getColumns();
                $dimensions     = $tables[$table]->getDimensions();
                $staticColumns  = $tables[$table]->getStaticColumns();
                $tree           = [
                    $mapper,
                    new Db\TreeToTable($columns, $dimensions, $staticColumns),
                ];
            }

            $pipelineArray = [
                $this->filterAttributesByTable($idField, $attributes),
                $tree,
                new Transform\Group($batchSize),
                new Action\SideEffect('insertRows', $table),
            ];

            $pipelines[] = new Flow\Pipeline($pipelineArray);
        }

        return [
            new Flow\Fork($pipelines),
        ];
    }

    protected function filterAttributesByTable($idField, $attributes)
    {
        return [
            new Transform\Map(function($item) use ($idField, $attributes) {
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

    protected function updatedIds($config)
    {
        $idField    = $config['idField'];
        $batchSize  = $config['batchSize'];

        return [
            new Transform\Map(function($item) use ($idField) {
                return $item['new'][$idField];
            }),
            new Transform\Group($batchSize),
            new Action\SideEffect('updatedIds'),
        ];
    }
}
