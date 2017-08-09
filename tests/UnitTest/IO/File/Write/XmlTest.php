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
        $items      = self::getTestData();

        $test->newInstance($testFile, ['indentXml' => true])
            ->testThatArgs($items)
            ->returnsGenerator()
            ->assertCallback(function($returnValue, $instance) use ($testFile, $items) {
                $content = file_get_contents($testFile);

                try {
                    $xml = simplexml_load_string($content);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }

                $errors = [];
                foreach ($items as $item) {
                    $errors = self::compareArrayToXml($item, $xml);
                }

                if ($errors) {
                    return $errors[0]['message'];
                }

                $validationErrors = self::getXmlErrors($content);

                if ($validationErrors) {
                    return $validationErrors[0]->message;
                }
            });

        if (is_file($testFile)) {
            unlink($testFile);
        }
    }


    protected static function compareArrayToXml($arr, $xml, &$errors = [])
    {
        $children = $xml->children();

        foreach ($arr as $name => $item) {

            if ($name === '@attributes') {
                $xmlAttributes = $xml->attributes();
                self::compareAttributes($item, $xmlAttributes, $errors);

                continue;
            }

            // If element has multiple children with same element name
            if (is_int($name)) {

                foreach ($item as $subName => $subItem) {
                    self::compareArrayToXml($subItem, $xml->$subName[$name], $errors);
                }

                continue;
            }

            if (!isset($children->$name)) {
                $errors[] = "Node {$name} does not exist";

                continue;
            }

            if (!is_array($item)) {
                $data = (string) $children->$name;
                if ($item !== $data) {
                    $errors[] = "{$item} is not the same as {$data}";
                }

                continue;
            }

            self::compareArrayToXml($item, $xml->$name, $errors);
        }

        return $errors;
    }

    protected static function compareAttributes($attributes, $xmlAttributes, &$errors)
    {
        foreach ($attributes as $attributeKey => $value) {

            if (!isset($xmlAttributes[$attributeKey])) {
                $errors[] = "{$attributeKey} is not set";
                continue;
            }

            $xmlAttribute = (string) $xmlAttributes[$attributeKey];
            if ($xmlAttribute !== $value) {
                $errors[] = "{$attributeKey} is not {$value}";
            }
        }
    }

    protected static function getXmlErrors($content, $version = '1.0', $encoding = 'utf-8')
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($content);

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return $errors;
    }

    protected static function getTestData()
    {
        return [
            'Order' => [
                'CustomerNo'    => '1234',
                '@attributes'   => ['an_attribute' => 'personen', 'qwe' => 'qwe'],
                'ExternalDocNo' => 'docno',
                'ShipToName'    => 'Hej Hejsson',
                'ShipToAddress' => 'Gatan 1',
                'PaymentMethod' => 'asfafa',
                'Lines' => [
                    [
                        'Line' => [
                            'ItemNo' => '89798',
                            'Quantity' => '4',
                        ]
                    ],
                    [
                        'Line' => [
                            'ItemNo' => '124',
                            'Quantity' => '1',
                        ]
                    ]
                ]
            ]
        ];
    }
}
