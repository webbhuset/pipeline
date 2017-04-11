<?php

/**
 * Pre Req:
 *      Attribute Set field must exist (attribute_set_id)
 *      Attribute Set id must exist in $sets
 */
namespace Webbhuset\Bifrost\Component\Sequence\Import\Table;

use Webbhuset\Bifrost\Component;
use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Type;

class Flat implements Component\ComponentInterface
{
    protected $pipeline;

    public function __construct(array $config)
    {
        $default = [
            'batchSize'         => 200,
            'shouldGroupInput'  => true,
        ];
        $config = array_replace($default, $config);

        $configType = new Type\StructType([
            'fields' => [
                'columns'       => new Type\SetType(['type' => new Type\StringType(['required' => true])]),
                'primaryKey'    => new Type\StringType(['required' => true]),
                'updateColumns' => new Type\SetType(['type' => new Type\StringType(['required' => true])]),
                'batchSize'     => new Type\IntType(['min_value' => 2, 'required' => true]),
            ],
        ]);

        $errors = $configType->getErrors($config);

        if ($errors) {
            throw new Type\TypeException('Constructor config is not correct', null, null, $errors);
        }

        if (!in_array($config['primaryKey'], $config['columns'])) {
            throw new BifrostException("Primary key {$config['primaryKey']} is not in columns.");
        }

        foreach ($config['updateColumns'] as $column) {
            if (!in_array($column, $config['columns'])) {
                throw new BifrostException("Update columns {$column} is not in columns.");
            }
        }

        $this->pipeline = $this->createPipeline($config);
    }

    public function process($items, $finalize = true)
    {
        return $this->pipeline->process($items, $finalize);
    }

    protected function createPipeline($config)
    {
        $columns            = $config['columns'];
        $primaryKey         = $config['primaryKey'];
        $updateColumns      = $config['updateColumns'];
        $batchSize          = $config['batchSize'];
        $shouldGroupInput   = $config['shouldGroupInput'];

        return new Component\Flow\Pipeline(array_filter([
            $shouldGroupInput ? new Component\Transform\Group($batchSize) : null,
            new Component\Transform\Map(function($rows) {
                return ['new' => $rows];
            }),
            $this->getOldData(),
            $this->diff($columns, $updateColumns, $primaryKey),
            new Component\Transform\Expand(function($rows) {
                foreach ($rows as $row) {
                    if (empty($row)) {
                        continue;
                    }
                    yield $row;
                }
            }),
            $this->createOrUpdate($config),
        ]));
    }

    protected function createOrUpdate($config)
    {
        $primaryKey         = $config['primaryKey'];
        $updateColumns      = $config['updateColumns'];
        $batchSize          = $config['batchSize'];

        return new Component\Flow\Fork([
            new Component\Flow\Pipeline([
                $this->filterByColumnValue($primaryKey, null),
                new Component\Transform\Group($batchSize),
                new Component\Transform\Merge(
                    new Component\Action\SideEffect('insertNewRows')
                ),
                $this->expandRows(),
                new Component\Action\Event('created'),
            ]),
            new Component\Flow\Pipeline([
                $this->filterByColumnValue($primaryKey, true),
                new Component\Transform\Group($batchSize),
                new Component\Action\SideEffect('updateRows', $updateColumns),
                $this->expandRows(),
                new Component\Action\Event('updated'),
            ]),
        ]);
    }

    protected function getOldData()
    {
        return new Component\Transform\Merge(
            new Component\Flow\Pipeline([
                new Component\Transform\Map(function($batch) {
                    return $batch['new'];
                }),
                new Component\Action\SideEffect('getOldData'),
                new Component\Transform\Map(function($rows) {
                    return ['old' => $rows];
                }),
            ])
        );
    }

    protected function diff($columns, $updateColumns, $primary)
    {
        $columns = array_fill_keys($columns, 1);

        return new Component\Transform\Map(function($batch) use ($columns, $updateColumns, $primary) {
            $old = $batch['old'];
            $new = $batch['new'];

            $updateRows = [];

            foreach ($new as $idx => $newRow) {
                $oldRow = $old[$idx];
                $pKey   = $oldRow[$primary];
                $newRow[$primary] = $pKey;

                if ($pKey === null) {
                    $updateRows[] = $newRow;
                    continue;
                }

                foreach ($updateColumns as $column) {
                    $oldValue = $oldRow[$column];
                    $newValue = $newRow[$column];
                    if ($oldValue != $newValue) {
                        $updateRows[] = $newRow;
                        break;
                    }
                }
            }

            return $updateRows;
        });
    }

    protected function expandRows()
    {
        return new Component\Transform\Expand(function($rows) {
            foreach ($rows as $row) {
                if (empty($row)) {
                    continue;
                }
                yield $row;
            }
        });
    }

    protected function filterByColumnValue($field, $value)
    {
        return new Component\Transform\Filter(function($item) use ($field, $value) {
            return $item[$field] == $value;
        });
    }
}
