<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class Mapper extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

    public function initTest()
    {
    }

    public function countTest()
    {
    }

    public function processNextTest()
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;
        $fields = [
            'name' => [
                'en_US' => '/title_en',
                'sv_SE' => '/title_sv',
                'nb_NO' => [$this, 'returnApa'],
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
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returns(Null);
    }

    public function processDataTest()
    {

        /* Test 1: Simple mapping and callback functions */
        $fields = [
            'name' => [
                'en_US' => '/title_en',
                'sv_SE' => '/title_sv',
                'nb_NO' => [$this, 'returnApa'],
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
            'parent'   => new NullProcessor([]),
        ];
        $this->newInstance($params)
            ->testThatArgs($indata)->returns($expectedOutput);


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
            'parent'   => new NullProcessor([]),
        ];
        $this->newInstance($params)
            ->testThatArgs($indata)->returns($expectedOutput);
    }

    public function finalizeTest()
    {
    }

    public function returnApa()
    {
        return 'Apa';
    }
}
