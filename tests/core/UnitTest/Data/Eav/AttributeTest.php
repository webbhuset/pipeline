<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Data\Eav;

use Webbhuset\Bifrost\Core\Data\Eav\Attribute\Scope;
use Webbhuset\Bifrost\Core\Type;

class AttributeTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs([])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs([
                'id'            => 1,
                'code'          => 'name',
                'backendType'   => 'varchar',
                'table'         => 'table',
                'scope'         => new \stdClass,
            ])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs([
                'id'            => 1,
                'code'          => 'name',
                'backendType'   => 'varchar',
                'scope'         => new Scope([]),
                'table'         => 'table',
            ])->notThrows('Webbhuset\\Bifrost\\Core\\BifrostException');
    }

    public static function getIdTest($test)
    {
    }

    public static function getCodeTest($test)
    {
    }

    public static function getTableTest($test)
    {
    }

    public static function getTypeObjectTest($test)
    {
        $data = [
            'id'            => 1,
            'code'          => 'name',
            'backendType'   => 'varchar',
            'scope'         => new Scope([]),
            'table'         => 'table',
        ];
        $ns = 'Webbhuset\\Bifrost\\Core\\Type\\';

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'StringType');

        $data['backendType'] = 'int';

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'IntType');

        $data['backendType'] = 'text';

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'StringType');

        $data['backendType'] = 'decimal';

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'FloatType\\DecimalType');

        $data['backendType'] = 'datetime';

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'StringType\\DateTimeType');

        $data['backendType'] = 'fisk';

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'StringType');

        $data['backendType'] = 'varchar';
        $data['typeObject'] = new Type\BoolType();

        $test->newInstance($data)
            ->testThatArgs()->returnsInstanceOf($ns.'BoolType');
    }

    public static function getBackendTypeTest($test)
    {
    }

    public static function getScopeTest($test)
    {
    }

    public static function shouldUpdateTest($test)
    {
    }
}
