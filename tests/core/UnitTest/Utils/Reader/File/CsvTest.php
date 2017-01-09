<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Reader\File;
use Webbhuset\Bifrost\Core\Utils\Reader\File\Csv;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;

class CsvTest extends Csv
{
    public function __construct(LoggerInterface $logger, $nextSteps, $params)
    {
        parent::__construct($logger, $nextSteps, $params);
        $args = [
            'filename' => dirname(__FILE__) . '/example.csv'
        ];
        $this->init($args);
    }

    public static function getEntityCountTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $test->newInstance($nullLogger, $mockProcessor, null)
            ->testThatArgs()->returnsValue(6);
    }

    public static function processNextTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $test->newInstance($nullLogger, $mockProcessor, null)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(false);
    }



    public static function initTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $args          = [
            'filename' => 'blablabla'
        ];
        $test->newInstance($nullLogger, $mockProcessor, null)
            ->testThatArgs($args)->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function getDataTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $test->newInstance($nullLogger, $mockProcessor, null);

        $expected = [
            [
                "name"          => "test1",
                "description"   => "description1",
                "sku"           => "0001-1",
                "price"         => "55",
                "qty"           => "2",
                "duplicate"     => "testa duplicate 1",
                "duplicate_1"   => "testa duplicate 2",
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $expected = [
            [
                "name"          => "test2",
                "description"   => "description2",
                "sku"           => "0001-2",
                "price"         => "66",
                "qty"           => "3",
                "duplicate"     => "testa duplicate 1",
                "duplicate_1"   => "testa duplicate 2",
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $expected = [
            [
                "name"          => "test3",
                "description"   => "description3",
                "sku"           => "0001-3",
                "price"         => "77",
                "qty"           => "4",
                "duplicate"     => "testa duplicate 1",
                "duplicate_1"   => "testa duplicate 2",
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $expected = [
            [
                "name"          => "test4",
                "description"   => "description4",
                "sku"           => "0001-4",
                "price"         => "88",
                "qty"           => "10",
                "duplicate"     => "testa duplicate 1",
                "duplicate_1"   => "testa duplicate 2",
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $expected = [
            [
                "name"          => "test5",
                "description"   => "description5",
                "sku"           => "0001-5",
                "price"         => "99",
                "qty"           => "23",
                "duplicate"     => "testa duplicate 1",
                "duplicate_1"   => "testa duplicate 2",
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $expected = [
            [
                "name"          => "test6",
                "description"   => "description6",
                "sku"           => "0001-6",
                "price"         => "87",
                "qty"           => "qty",
                "duplicate"     => "testa duplicate 1",
                "duplicate_1"   => "testa duplicate 2",
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $test->testThatArgs()->returnsStrictValue(false);
    }

    public static function finalizeTest()
    {
    }

    public static function __constructTest()
    {
    }
}
