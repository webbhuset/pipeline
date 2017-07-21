<?php

namespace Webbhuset\Whaskell\Test\UnitTest\IO\File\Fetch;

class DirectoryTest
{
    public static function __constructTest($test)
    {
    }

    public static function __invokeTest($test)
    {
        $config = [
            'pathname' => true,
            'relative' => true,
        ];
        $test->newInstance($config)
            ->testThatArgs(__DIR__.'/testfiles')
            ->returnsGenerator()
            ->returnsValue([
                0 => 'products-1022.csv',
                1 => 'products-1021.xml',
                2 => 'products-1021.csv',
                3 => 'Products-1025.XML',
                4 => 'Products-1025.CSV',
                5 => 'products-1023.xml',
                6 => 'products-1020.xml',
                7 => 'products-1023.csv',
                8 => 'products-1022.xml',
                9 => 'products-1024.xml',
                10 => 'products-1024.csv',
                11 => 'products-1020.csv',
            ]);

        $config = [
            'pathname'  => true,
            'recursive' => true,
            'relative'  => true,
        ];
        $test->newInstance($config)
            ->testThatArgs(__DIR__.'/testfiles')
            ->returnsGenerator()
            ->returnsValue([
                0 => 'products-1022.csv',
                1 => 'products-1021.xml',
                2 => 'products-1021.csv',
                3 => 'Products-1025.XML',
                4 => 'Products-1025.CSV',
                5 => 'subdir/products/data-11.csv',
                6 => 'subdir/products/data-10.xml',
                7 => 'subdir/products/data-10.csv',
                8 => 'subdir/products/data-12.csv',
                9 => 'products-1023.xml',
                10 => 'products-1020.xml',
                11 => 'products-1023.csv',
                12 => 'products-1022.xml',
                13 => 'products-1024.xml',
                14 => 'products-1024.csv',
                15 => 'products-1020.csv',
            ]);
    }
}
