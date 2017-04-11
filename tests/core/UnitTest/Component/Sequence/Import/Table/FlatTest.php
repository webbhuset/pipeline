<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Sequence\Import\Table;

use Webbhuset\Bifrost\Component;
use Webbhuset\Bifrost\Test\Helper\Component\AssertComponent;
use Exception;

class FlatTest
{
    public static function __constructTest($test)
    {
        $config = [
            'columns' => [
                'primary',
                'key1',
                'key2',
                'col1',
                'col2',
                'col3',
            ],
            'primaryKey'    => 'primary',
            'updateColumns' => [
                'col1',
                'col2',
            ]
        ];

        $test
            ->testThatArgs($config)
            ->notThrows('Exception');

        $config['primaryKey'] = [];

        $test
            ->testThatArgs($config)
            ->throws('Webbhuset\\Bifrost\\Core\\Type\\TypeException');

        $config['primaryKey'] = 'not in columns';

        $test
            ->testThatArgs($config)
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');

        $config['primaryKey'] = 'primary';
        $config['updateColumns'] = ['col1', 'other'];

        $test
            ->testThatArgs($config)
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');
    }

    public static function processTest($test)
    {
    }

    public static function createPipelineTest($test)
    {
        $rows = [
            [
                'key1' => 11,
                'key2' => 20,
                'col1' => 'value 1',
                'col2' => 'value 2',
                'col3' => 'value 3',
                'other' => 'Data From other Table',
            ],
            [
                'key1' => 11,
                'key2' => 21,
                'col1' => 'value 4',
                'col2' => 'value 5',
                'col3' => 'value 6',
                'other' => 'Data From other Table',
            ],
            [
                'key1' => 12,
                'key2' => 20,
                'col1' => 'value 7',
                'col2' => 'value 8',
                'col3' => 'value 9',
                'other' => 'Data From other Table',
            ],
            [
                'key1' => 12,
                'key2' => 21,
                'col1' => 'value 10',
                'col2' => 'value 11',
                'col3' => 'value 12',
                'other' => 'Data From other Table',
            ],
        ];

        $config = [
            'columns' => [
                'primary',
                'key1',
                'key2',
                'col1',
                'col2',
                'col3',
            ],
            'primaryKey'    => 'primary',
            'updateColumns' => [
                'col1',
                'col2',
            ],
            'batchSize' => 20,
            'shouldGroupInput' => true,
        ];
        $columns = $config['columns'];
        $monad = [
            'getOldData' => function($newRows) use ($rows, $columns) {
                $rows[0]['primary'] = 100;
                $rows[1]['primary'] = 200;
                $rows[1]['col1']    = 'other value';
                $rows[2] = array_fill_keys($columns, null);
                $rows[3]['primary'] = 300;
                $rows[3]['col3']    = 'ignored value';

                return $rows;
            },
            'insertNewRows' => function ($rows) {
                return [
                    ['primary' => 'created'],
                ];
            }
        ];
        $expected = [
            [
                'key1' => 12,
                'key2' => 20,
                'col1' => 'value 7',
                'col2' => 'value 8',
                'col3' => 'value 9',
                'primary' => 'created',
            ],
            [
                'key1' => 11,
                'key2' => 21,
                'col1' => 'value 4',
                'col2' => 'value 5',
                'col3' => 'value 6',
                'primary' => 200,
            ],
        ];

        $test->newInstance($config)
            ->testThatArgs($config)
            ->assertCallback(AssertComponent::makeAssert($rows, $expected, $monad));
    }
}
