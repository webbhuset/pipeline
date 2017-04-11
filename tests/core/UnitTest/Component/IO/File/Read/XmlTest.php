<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\IO\File\Read;

class XmlTest
{
    public static function __constructTest($test)
    {
    }

    public static function processTest($test)
    {
        $test->newInstance('products/simple')
            ->testThatArgs(__DIR__ . '/example.xml')
            ->returnsGenerator()
            ->returnsStrictValue([
                [
                  'simple' => [
                    'name' => 'test1',
                    'description' => 'description1',
                    'sku' => '0001-1',
                    'price' => '55',
                    'qty' => '2',
                    'related' => [
                      'sku' => [
                        0 => '1001-2',
                        1 => '1001-3',
                      ],
                    ],
                  ],
                ],
                [
                  'simple' => [
                    'name' => 'test2',
                    'description' => 'description2',
                    'sku' => '0001-2',
                    'price' => '66',
                    'qty' => '3',
                  ],
                ],
                [
                  'simple' => [
                    'name' => 'test3',
                    'description' => 'description3',
                    'sku' => '0001-3',
                    'price' => '77',
                    'qty' => '4',
                  ],
                ],
                [
                  'simple' => [
                    'name' => 'test4',
                    'description' => 'description4',
                    'sku' => '0001-4',
                    'price' => '88',
                    'qty' => '10',
                  ],
                ],
                [
                  'simple' => [
                    'name' => 'test5',
                    'description' => 'description5',
                    'sku' => '0001-5',
                    'price' => '99',
                    'qty' => '23',
                  ],
                ],
                [
                  'simple' => [
                    'name' => 'test6',
                    'description' => 'description6',
                    'sku' => '0001-6',
                    'price' => '87',
                    'qty' => 'qty',
                  ],
                ],
            ]);

        $test->newInstance('products/configurable')
            ->testThatArgs(__DIR__ . '/example.xml')
            ->returnsGenerator()
            ->returnsStrictValue([
                [
                  'configurable' => [
                    'name' => 'test5',
                    'description' => 'description5',
                    'sku' => '0001-5',
                    'price' => '99',
                    'children' => [
                      'simple' => [
                        0 => '10-10',
                        1 => '10-11',
                        2 => '10-12',
                      ],
                    ],
                    'qty' => '23',
                  ],
                ],
            ]);

    }
}
