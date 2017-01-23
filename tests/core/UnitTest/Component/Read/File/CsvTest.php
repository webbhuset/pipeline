<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\Read\File;

class CsvTest
{
    public static function __constructTest($test)
    {

    }

    public static function processTest($test)
    {
        $testFile = __DIR__ . '/example.csv';

        $test->newInstance([])
            ->testThatArgs(['non-existsing-file.csv'])
            ->returnsGenerator(true)
            ->returnsArray();

        $test->newInstance(['column_count' => 7])
            ->testThatArgs([__DIR__ . '/example.xml'])
            ->returnsGenerator()
            ->returnsArray();

        $test->newInstance(['separator' => ';', 'enclosure' => '"'])
            ->testThatArgs([__DIR__ . '/example.csv'])
            ->returnsGenerator()
            ->returnsStrictValue([
                [
                  'name'        => 'test1',
                  'description' => 'description1',
                  'sku'         => '0001-1',
                  'price'       => '55',
                  'qty'         => '2',
                  'duplicate'   => 'testa duplicate 1',
                  'duplicate_1' => 'testa duplicate 2',
                ],
                [
                  'name'        => 'test2',
                  'description' => 'description2',
                  'sku'         => '0001-2',
                  'price'       => '66',
                  'qty'         => '3',
                  'duplicate'   => 'testa duplicate 1',
                  'duplicate_1' => 'testa duplicate 2',
                ],
                [
                  'name'        => 'test3',
                  'description' => 'description3',
                  'sku'         => '0001-3',
                  'price'       => '77',
                  'qty'         => '4',
                  'duplicate'   => 'testa duplicate 1',
                  'duplicate_1' => 'testa duplicate 2',
                ],
                [
                  'name'        => 'test4',
                  'description' => 'description4',
                  'sku'         => '0001-4',
                  'price'       => '88',
                  'qty'         => '10',
                  'duplicate'   => 'testa duplicate 1',
                  'duplicate_1' => 'testa duplicate 2',
                ],
                [
                  'name'        => 'test5',
                  'description' => 'description5',
                  'sku'         => '0001-5',
                  'price'       => '99',
                  'qty'         => '23',
                  'duplicate'   => 'testa duplicate 1',
                  'duplicate_1' => 'testa duplicate 2',
                ],
                [
                  'name'        => 'test6',
                  'description' => 'description6',
                  'sku'         => '0001-6',
                  'price'       => '87',
                  'qty'         => 'qty',
                  'duplicate'   => 'testa duplicate 1',
                  'duplicate_1' => 'testa duplicate 2',
                ],
            ]);
    }
}
