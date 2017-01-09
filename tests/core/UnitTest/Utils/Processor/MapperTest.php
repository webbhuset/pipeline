<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;

class MapperTest
{
    public static function __constructTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $params        = [];
        $test
            ->testThatArgs($nullLogger, $mockProcessor, $params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function processNextTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $fields = [
            'name' => [
                'en_US' => '/title_en',
                'sv_SE' => '/title_sv',
                'nb_NO' => [new MapperTest, 'returnApa'],
            ],
        ];
        $indata = [
            [
                'title_en'  => 'a title',
                'title_sv'  => 'en titel',
                'nonsense'  => '1231asdavasdadbs'
            ]
        ];
        $params = [
            'fields'   => $fields,
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsValue(Null);
    }

    public static function processDataTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];

        /* Test 1: Simple mapping and callback functions */
        $fields = [
            'name' => [
                'en_US' => '/title_en',
                'sv_SE' => '/title_sv',
                'nb_NO' => [new MapperTest, 'returnApa'],
            ],
        ];
        $indata = [
            'title_en'  => 'a title',
            'title_sv'  => 'en titel',
            'nonsense'  => '1231asdavasdadbs'
        ];
        $expectedOutput = [
            'name' => [
                'en_US' => 'a title',
                'sv_SE' => 'en titel',
                'nb_NO' => 'Apa',
            ],
        ];
        $params = [
            'fields'   => $fields,
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsValue($expectedOutput);


        /* Test 2: Path system for getting correct value from indata */
        $fields = [
            'price' => [
                'test' => [
                    'test' => [
                        'USD' => '#price#usd#special',
                        'SEK' => '/price/sek',
                        'NOK' => '/price_nok',
                    ]
                ]
            ],
        ];
        $indata = [
            'price_nok' => null,
            'price' => [
                'sek' => 55,
                'usd' => [
                    'special' => 37.1,
                    'annat'   => 98.44,
                ],
            ],
            'nonsense' => '1231asdavasdadbs'
        ];
        $expectedOutput = [
            'price' => [
                'test' => [
                    'test' => [
                        'USD' => 37.1,
                        'SEK' => 55,
                        'NOK' => null,
                    ]
                ]
            ],
        ];
        $params = [
            'fields'   => $fields,
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsValue($expectedOutput);
    }

    public static function finalizeTest()
    {
    }

    public static function initTest()
    {
    }

    public static function countTest()
    {
    }

    public function returnApa()
    {
        return 'Apa';
    }
}
