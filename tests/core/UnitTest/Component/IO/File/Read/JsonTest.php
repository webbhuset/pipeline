<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\IO\File\Read;

use Webbhuset\Bifrost\Core\Component\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Component\Logger\LoggerInterface;
use Webbhuset\Bifrost\Core\Component\Writer\Mock\Collector;

class JsonTest extends Json
{
    public function __construct(LoggerInterface $logger, $nextSteps, $params)
    {
        parent::__construct($logger, $nextSteps, $params);
        $args = [
            'filename' => dirname(__FILE__) . '/example.json'
        ];
        $this->init($args);
    }

    public static function getEntityCountTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockComponent = [new Collector];
        $test->newInstance($nullLogger, $mockComponent, null)
            ->testThatArgs()->returnsValue(6);
    }

    public static function processNextTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockComponent = [new Collector];
        $test->newInstance($nullLogger, $mockComponent, null)
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
        $mockComponent = [new Collector];
        $args          = [
            'filename' => 'blablabla'
        ];
        $test->newInstance($nullLogger, $mockComponent, null)
            ->testThatArgs($args)->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function getDataTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockComponent = [new Collector];
        $test->newInstance($nullLogger, $mockComponent, null);

        $expected = [
            [
                "name"          => "test1",
                "description"   => "description1",
                "sku"           => "0001-1",
                "price"         => "55",
                "qty"           => "2",
            ]
        ];
        $test->testThatArgs()->returnsValue($expected);

        $expected = [
            [
                "name"          => "test2",
                "description"   => "description2",
                "sku"           => "0001-2",
                "price"         => "66",
                "qty"           => "3",
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
            ]
        ];
        $test->testThatArgs()->returnsStrictValue($expected);

        $test->testThatArgs()->returnsStrictValue(false);
    }

    public static function finalizeTest()
    {
    }

    public static function startDocumentTest()
    {
    }

    public static function startArrayTest()
    {
    }

    public static function endDocumentTest()
    {
    }

    public static function startObjectTest()
    {
    }

    public static function endObjectTest()
    {
    }

    public static function endArrayTest()
    {
    }

    public static function keyTest()
    {
    }

    public static function valueTest()
    {
    }

    public static function whitespaceTest()
    {
    }
}
