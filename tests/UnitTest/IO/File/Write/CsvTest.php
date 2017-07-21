<?php

namespace Webbhuset\Whaskell\Test\UnitTest\IO\File\Write;

class CsvTest
{
    public static function __constructTest($test)
    {

    }

    public static function __invokeTest($test)
    {
        $testFile   = __DIR__.'/testfile.csv';
        $items      = self::getTestData();

        $test->newInstance($testFile)
            ->testThatArgs($items)
            ->returnsGenerator()
            ->assertCallback(function($returnValue, $instance) use ($testFile, $items) {
                $file = fopen($testFile, 'r');
                $row  = fgetcsv($file);
                $headers = array_keys($items[0]);

                if ($row != $headers) {
                    return 'Headers are not equal';
                }

                $idx = 0;

                while ($row = fgetcsv($file)) {
                    if (array_values($items[$idx]) != $row) {
                        return 'Rows are not equal';
                    }
                    $idx += 1;
                }
            });
        ;

        if (is_file($testFile)) {
            unlink($testFile);
        }
    }

    protected static function getTestData()
    {
        return [
            [
                'na"me'  => 'APA HEJ',
                'sk,u'   => '100,01',
                'price' => '199.95',
            ],
            [
                'name'  => 'Product 2',
                'sku'   => '10002',
                'price' => '199.95',
            ],
            [
                'name'  => 'Product 3',
                'sku'   => '10003',
                'price' => '199.95',
            ],
        ];
    }
}
