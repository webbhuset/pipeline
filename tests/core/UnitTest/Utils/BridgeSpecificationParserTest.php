<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils;

class BridgeSpecificationParserTest
{
    public static function parseTest($test)
    {
        $expected = [
            "name" =>  "aaa",
            "children" =>  [
                [
                    "name" =>  "bbb",
                    "children" =>  [
                        [
                            "name" =>  "ccc",
                            "children" =>  [
                                [
                                    "name" =>  "ddd",
                                    "children" =>  []
                                ],
                                [
                                    "name" =>  "ddd",
                                    "children" =>  [
                                        [
                                            "name" =>  "fff",
                                            "children" =>  []
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $indata = <<<BRIDGE
aaa->bbb->ccc
    ->ddd
    ->ddd->fff
BRIDGE;

        $test->testThatArgs($indata)->returnsValue($expected);
    }
}
