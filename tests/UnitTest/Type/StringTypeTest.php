<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class StringTypeTest
{
    public static function __constructTest($test)
    {

    }

    public static function isEqualTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs('apa123', 'apa123')->returnsValue(true)
            ->testThatArgs('', '')->returnsValue(true)
            ->testThatArgs('apa123', 'apa')->returnsValue(false)
            ->testThatArgs('apa', 'apa123')->returnsValue(false)
            ->testThatArgs(null, '')->returnsValue(false)
            ->testThatArgs('', null)->returnsValue(false)
            ->testThatArgs(0, '')->throws('Webbhuset\Bifrost\BifrostException')
            ->testThatArgs(0, [])->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs('apa123')->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false)
            ->testThatArgs(12)->notReturnsValue(false)
            ->testThatArgs(12.123)->notReturnsValue(false)
            ->testThatArgs(['12'])->notReturnsValue(false);

        $test->newInstance()
            ->testThatArgs('apa123')->returnsValue(false)
            ->testThatArgs(null)->notReturnsValue(false)
            ->testThatArgs(12)->notReturnsValue(false)
            ->testThatArgs(12.123)->notReturnsValue(false)
            ->testThatArgs(['12'])->notReturnsValue(false);

        $test->newInstance(T::MIN(4), T::NULLABLE)
            ->testThatArgs('apa')->notReturnsValue(false)
            ->testThatArgs('apa123')->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false);

        $test->newInstance(T::MAX(4), T::NULLABLE)
            ->testThatArgs('apa')->returnsValue(false)
            ->testThatArgs('apa123')->notReturnsValue(false)
            ->testThatArgs(null)->returnsValue(false);


        $matches = [
            '/\d-\d/' => 'Value must be two numbers.',
            '/-/' => 'Value must be two numbers.',
        ];

        $test->newInstance(T::MATCH($matches), T::NULLABLE)
            ->testThatArgs('apa')->returnsValue('Value must be two numbers.')
            ->testThatArgs('1-a')->returnsValue('Value must be two numbers.')
            ->testThatArgs('1-1')->returnsFalse()
        ;
        $notMatches =  [
            '/(^\s+\S)|(\S\s+$)/' => 'Trailing whitespace',
        ];

        $test->newInstance(T::NOTMATCH($notMatches), T::NULLABLE)
            ->testThatArgs(' apa')->returnsValue('Trailing whitespace')
            ->testThatArgs(' apa ')->returnsValue('Trailing whitespace')
            ->testThatArgs('apa ')->returnsValue('Trailing whitespace')
            ->testThatArgs('a p a')->returnsFalse()
        ;
    }

    public static function castTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs('apa123')->returnsValue('apa123')
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs(123)->returnsValue('123')
            ->testThatArgs(12.335)->returnsValue('12.335');

        $test->newInstance()
            ->testThatArgs('apa123')->returnsValue('apa123')
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs(123)->returnsValue('123')
            ->testThatArgs(12.335)->returnsValue('12.335');
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs([], null)->throws('Webbhuset\Bifrost\BifrostException');
    }
}
