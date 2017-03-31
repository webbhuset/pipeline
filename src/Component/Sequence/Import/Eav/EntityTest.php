<?php

namespace Webbhuset\Bifrost\Core\Component\Sequence\Import\Eav;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Component\Action;
use Webbhuset\Bifrost\Core\Component\Db;
use Webbhuset\Bifrost\Core\Component\Dev;
use Webbhuset\Bifrost\Core\Component\Flow;
use Webbhuset\Bifrost\Core\Component\Transform;
use Webbhuset\Bifrost\Core\Helper;

class EntityTest implements ComponentInterface
{
    protected $component;

    public function __construct(array $config)
    {
        $default = [
            'idField'               => 'entity_id',
            'setField'              => 'attribute_set_id',
            'updateOnly'            => false,
            'attributesToUpdate'    => [],
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

        return new Flow\Fork([
            new Flow\Pipeline([
                new Transform\Filter(function($item) use ($idField) {
                    return $item[$idField] === null;
                }),
                $this->createEntities($updateOnly)
            ]),
            new Flow\Pipeline([
                new Transform\Filter(function($item) use ($idField) {
                    return $item[$idField] !== null;
                }),
                $this->updateEntities($idField, $setField, $entityTable, $attributesToUpdate, $attributesByTable, $dimensions, $attributeSets),
            ])
        ]);
    }

    public function process($items, $finalize = true)
    {
        return $this->component->process($items, $finalize);
    }

    protected function createEntities($updateOnly)
    {
        if ($updateOnly) {
            return [
                new Action\Event('notFound'),
            ];
        } else {
            return [
                $this->insertNewEntities(),
                new Action\Event('created'),
                $this->fillAttributeNull(),
                $this->insertAttributes(),
            ];
        }
    }

    protected function updateEntities($idField, $setField, $entityTable, $attributesToUpdate, $attributesByTable, $dimensions, $attributeSets)
    {
        return [
            $this->getOldAttributeData(),
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
            new Flow\CaseSwitch([
                [
                    function($item) {
                        return !$item['diff'];
                    },
                    new Flow\Pipeline([
                        new Action\Event('skipped'),
                    ])
                ],
                [
                    function($item) {
                        return $item['diff'];
                    },
                    new Flow\Pipeline([
                        new Action\Event('updated'),
                        new Flow\Fork([
                            new Flow\Pipeline($this->insertAttributes($idField, $entityTable, $attributesByTable, $dimensions)),
                            new Flow\Pipeline($this->updateUpdatedAt($idField)),
                        ])
                    ])
                ]
            ])
        ];
    }

    protected function insertNewEntities()
    {
        return [
            new Transform\Group(1000),
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

    protected function fillAttributeNull()
    {
        return [
            new Transform\Map(function($item) {
                if (!isset($item['price'])) {
                    $item['price'][0] = null;
                }

                return $item;
            }),
        ];
    }

    protected function getOldAttributeData()
    {
        return [
            new Transform\Group(1000),
            new Transform\Map(function($items) {
                return ['new' => $items];
            }),
            new Transform\Merge(
                new Flow\Pipeline([
                    new Transform\Map(function($items) {
                        $ids = [];
                        foreach ($items['new'] as $item) {
                            $ids[] = $item['entity_id'];
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

    protected function insertAttributes($idField, $entityTable, $attributesByTable, $dimensions)
    {
        $pipelines = [];

        foreach ($attributesByTable as $table => $attribute) {
            $pipelineArray = [
                $this->filterAttributesByTable($idField, $attributesByTable[$table]),
            ];
            if ($table == $entityTable) {
                $pipelineArray = array_merge($pipelineArray, [
                    new Action\SideEffect('updateEntityRow', $table),
                ]);
            } else {
                $pipelineArray = array_merge($pipelineArray, [
                    $this->filterAttributesByTable($idField, $attributesByTable[$table]),
                    $this->expandEavAttributes($idField),
                    new Db\TreeToTable($dimensions, $dimensions),
                    new Transform\Group(1000),
                    new Action\SideEffect('insertAttributeRows', $table),
                ]);
            }

            $pipelines[] = new Flow\Pipeline($pipelineArray);
        }

        return [
            $this->mapDiff($idField),
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
                $newItem = [
                    $idField => $item[$idField]
                ];

                foreach ($attributes as $attribute) {
                    if (isset($item[$attribute])) {
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

    protected function expandEavAttributes($idField)
    {
        return new Transform\Expand(function($item) use ($idField) {
            $attributes = array_keys($item);
            foreach ($attributes as $attribute) {
                if ($attribute == $idField) {
                    continue;
                }
                yield [
                    $item[$idField] => [
                        $attribute => $item[$attribute],
                    ],
                ];
            }
        });
    }

    protected function updateUpdatedAt($idField)
    {
        return [
            new Transform\Map(function($item) use ($idField) {
                return $item['new'][$idField];
            }),
            new Transform\Group(1000),
            new Action\SideEffect('updateUpdatedAt'),
        ];
    }
}
