<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Data\Eav\Attribute;

class ScopeTest
{
    public static function __constructTest($test)
    {
    }

    public static function mapTest($test)
    {
        $localeMap = [
            'en_US' => [0],
            'sv_SE' => [1, 4, 5],
            'nb_NO' => [2, 3],
        ];

        $test->newInstance($localeMap, 'en_US')
            ->testThatArgs([
                'en_US' => 'English value',
                'sv_SE' => 'Swedish value',
            ])
            ->returnsValue([
                0 => 'English value',
                1 => 'Swedish value',
                4 => 'Swedish value',
                5 => 'Swedish value',
            ]);

        $test
            ->testThatArgs([
                'en_US' => 'English value',
                'es_ES' => 'Spanish value',
            ])
            ->returnsValue([
                0 => 'English value',
            ]);

        $test
            ->testThatArgs('English value')
            ->returnsValue([
                0 => 'English value',
            ]);

        $test
            ->testThatArgs([
                'nb_NO' => 'Norwegian value',
                'en_US' => 'English value',
                'sv_SE' => 'Swedish value',
            ])
            ->returnsValue([
                0 => 'English value',
                1 => 'Swedish value',
                2 => 'Norwegian value',
                3 => 'Norwegian value',
                4 => 'Swedish value',
                5 => 'Swedish value',
            ]);

        $test
            ->testThatArgs([
                'sv_SE' => 'Swedish value',
                'nb_NO' => 'Norwegian value',
            ])
            ->returnsValue([
                1 => 'Swedish value',
                2 => 'Norwegian value',
                3 => 'Norwegian value',
                4 => 'Swedish value',
                5 => 'Swedish value',
            ]);

        /**
         * @testCase Test global scope
         */
        $test
            ->newInstance([])
            ->testThatArgs('Global value')
            ->returnsValue([
                0 => 'Global value',
            ]);

        $test
            ->newInstance([])
            ->testThatArgs(['Global value'])
            ->returnsValue([
                0 => 'Global value',
            ]);
    }
}
