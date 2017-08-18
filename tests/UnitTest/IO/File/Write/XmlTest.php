<?php

namespace Webbhuset\Whaskell\Test\UnitTest\IO\File\Write;

class XmlTest
{
    public static function __constructTest($test)
    {

    }

    public static function __invokeTest($test)
    {
        $testFile   = __DIR__.'/example.xml';
        $items      = self::getTestData('name');

        $test->newInstance($testFile, ['root' => 'RootElement', 'indentXml' => true])
            ->testThatArgs($items)
            ->returnsGenerator()
            ->assertCallback(function($returnValue, $instance) use ($testFile, $items) {
                $content = trim(file_get_contents($testFile));
                $correctXML = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<RootElement>
 <valid>valid</valid>
 <v4lid>valid</v4lid>
 <välid>valid</välid>
 <v.lid>valid</v.lid>
 <v-lid>valid</v-lid>
 <v_lid>valid</v_lid>
 <val.d valid="&lt;va&quot;=&amp;&lt;&gt;lue&gt;">&lt;&gt;&amp;=</val.d>
</RootElement>
XML;
                if ($content != trim($correctXML)) {
                    return "XML does not match";
                }
            });

        if (is_file($testFile)) {
            unlink($testFile);
        }
    }

    protected static function getTestData($type)
    {
        yield [
            '.invalid'      => 'invalid',
            'invalid.'      => 'invalid',
            '1invalid'      => 'invalid',
            'invalid2'      => 'invalid',
            'XMLinvalid'    => 'invalid',
            '_invalid'      => 'invalid',
            'invalid-'      => 'invalid',
            'in<alid'       => 'invalid',
            'in valid'      => 'invalid',
            '012'           => 'invalid',
            'valid'         => 'valid',
            'v4lid'         => 'valid',
            'välid'         => 'valid',
            'v.lid'         => 'valid',
            'v-lid'         => 'valid',
            'v_lid'         => 'valid',
            'val.d'         => [
                '@attributes' => ['valid' => '<va"=&<>lue>', 'in"valid' => 'invalid', 'in=valid' => 'invalid'],
                '<>&=',
            ],
        ];
    }
}
