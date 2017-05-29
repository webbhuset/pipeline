<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Helper\ArrayHelper;

class DataAccessTest
{
    public static function __constructTest($test)
    {
    }

    public static function offsetGetTest($test)
    {
        $data = [
            'firstLevel' => 11,
            'nested' => [
                'key' => [
                    'with' => [
                        'multiple' => [
                            'levels' => 22,
                        ],
                    ],
                ],
            ],
        ];

        /**
         * @testCase Value is returned from key. If key does not exist, null is returned.
         */
        $test
            ->newInstance($data)
            ->testThatArgs('firstLevel')->returnsStrictValue(11)
            ->testThatArgs('nonExistingKey')->returnsNull()

            /**
             * @testCase Array can be used as a key path to access nested values.
             */
            ->testThatArgs(['firstLevel'])->returnsStrictValue(11)
            ->testThatArgs(['nested', 'key', 'with', 'multiple', 'levels'])->returnsStrictValue(22)
            ->testThatArgs(['nested', 'key', 'with', 'multiple'])->returnsStrictValue(['levels' => 22])
            ->testThatArgs('nested/key/with/multiple/levels')->returnsNull()

            /**
             * @testCase A path separator can be passed to the constructor for nested access.
             */
            ->newInstance($data, '/')
            ->testThatArgs('nested/key/with/multiple/levels')->returnsStrictValue(22)
            ->testThatArgs('nested/key/with/multiple')->returnsStrictValue(['levels' => 22])
            ->testThatArgs(['nested', 'key', 'with', 'multiple', 'levels'])->returnsStrictValue(22)
            ->testThatArgs('firstLevel')->returnsStrictValue(11)
            ->testThatArgs(['firstLevel'])->returnsStrictValue(11)

            /**
             * @testCase Value returned if a key is not present can be changed with a constructor parameter.
             */
            ->newInstance($data, null, 'default')
            ->testThatArgs('nonExistingKey')->returnsValue('default')
        ;
    }

    public static function offsetSetTest($test)
    {
    }

    public static function offsetExistsTest($test)
    {
    }

    public static function offsetUnsetTest($test)
    {
    }
}
