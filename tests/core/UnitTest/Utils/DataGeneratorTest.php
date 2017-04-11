<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils;
use \Webbhuset\Bifrost\Utils\DataGenerator;

class DataGeneratorTest
{
    public static function getStringTest($test)
    {
        $generator = new DataGenerator;
        $expected = $generator->getString(4, 55, 'apa');

        $test->newInstance()
            ->testThatArgs(4, 55, 'apa')->returnsValue($expected)->returnsString()
            ->testThatArgs(4, 55, 'apa123')->notReturnsValue($expected)->returnsString()
            ->testThatArgs(4, 55, 'apa')->returnsValue($expected)->returnsString();
    }

    public static function getIntTest($test)
    {
        $generator = new DataGenerator;
        $expected  = $generator->getInt(4, 550, 'apa');

        $test->newInstance()
            ->testThatArgs(4, 550, 'apa')->returnsValue($expected)->returnsInt()
            ->testThatArgs(4, 550, 'apa123')->notReturnsValue($expected)->returnsInt()
            ->testThatArgs(4, 550, 'apa')->returnsValue($expected)->returnsInt();
    }

    public static function getBoolTest($test)
    {
        $generator = new DataGenerator;
        $expected  = $generator->getBool('apa');

        $test->newInstance()
            ->testThatArgs('apa')->returnsValue($expected)->returnsBool();
    }

    public static function getDateTest($test)
    {
        $generator = new DataGenerator;
        $expected  = $generator->getDate('1960-01-01', '2016-12-31', 'apa');

        $test->newInstance()
            ->testThatArgs('1960-01-01', '2016-12-31', 'apa')->returnsValue($expected)->returnsString()
            ->testThatArgs('1960-01-01', '2016-12-31', 'apa123')->notReturnsValue($expected)->returnsString()
            ->testThatArgs('1960-01-01', '2016-12-31', 'apa')->returnsValue($expected)->returnsString();
    }

    public static function getFloatTest($test)
    {
        $generator = new DataGenerator;
        $expected  = $generator->getFloat(-41, 59, 'apa');

        $test->newInstance()
            ->testThatArgs(-41, 59, 'apa')->returnsValue($expected)->returnsFloat()
            ->testThatArgs(-41, 59, 'apa213')->notReturnsValue($expected)->returnsFloat()
            ->testThatArgs(-41, 59, 'apa')->returnsValue($expected)->returnsFloat();
    }

    public static function setGlobalSeedTest($test)
    {
    }

    public static function setRowSeedTest($test)
    {
    }

    public static function getRandomBytesGeneratorTest($test)
    {
    }

    public static function getLettersTest($test)
    {
    }

    public static function getWordTest($test)
    {
    }

    public static function getSentenceTest($test)
    {
        $test->newInstance()
            ->testThatArgs(mt_rand())
            ->returnsString();
    }

    public static function getParagraphTest($test)
    {
        $test->newInstance()
            ->testThatArgs(mt_rand())
            ->returnsString();
    }
}
