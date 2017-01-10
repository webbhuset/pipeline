<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Writer;

use Webbhuset\Bifrost\Core\Utils\Logger;
use Webbhuset\Bifrost\Core\Utils\Writer\TableDeleter;

class TableDeleterTest extends TableDeleter
{
    public function __construct(Logger\LoggerInterface $logger, $params)
    {
        /**
        * The following code has been commented out to be able to test anythin without adapter:
        *
        *
        * if (!isset($params['adapter'])) {
        *    throw new BifrostException("Adapter parameter is not set.");
        * }
        * if (!$params['adapter'] instanceof \Varien_Db_Adapter_Pdo_Mysql) {
        *     throw new BifrostException("Adapter must be a instance of Varien_Db_Adapter_Pdo_Mysql");
        * }
        */

        if (!isset($params['table_name'])) {
            throw new BifrostException("Table name parameter is not set.");
        }
        if (!is_string($params['table_name'])) {
            throw new BifrostException("Table name must be a string");
        }

        if (!isset($params['columns'])) {
            throw new BifrostException("Columns parameter must be set.");
        }
        if (!is_array($params['columns'])) {
            throw new BifrostException("Columns parameter must be array.");
        }

        //$this->adapter      = $params['adapter'];
        $this->table_name   = $params['table_name'];
        $this->columns      = $params['columns'];
    }

    public static function getWhereTest($test)
    {
        $logger = new Logger\NullLogger();
        $params = [
            'table_name' => 'djur',
            'columns'    => [
                'apa',
                'fisk',
                'hund',
                'katt',
            ]
        ];
        $test->newInstance($logger, $params)
            ->testThatArgs()->returnsValue('apa = :apa AND fisk = :fisk AND hund = :hund AND katt = :katt');

        $params = [
                'table_name' => 'djur',
                'columns'    => [
                    "apa'",
                    'fisk',
                    'hund',
                    'katt',
                ]
            ];
        $test->newInstance($logger, $params)
            ->testThatArgs()->throws('Webbhuset\Bifrost\Core\BifrostException');

        $params = [
                'table_name' => 'djur',
                'columns'    => [
                    ":apa",
                    'fisk',
                    'hund',
                    'katt',
                ]
            ];
        $test->newInstance($logger, $params)
            ->testThatArgs()->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function getBindTest($test)
    {
        $logger = new Logger\NullLogger();
        $params = [
            'table_name' => 'djur',
            'columns'    => [
                'apa',
                'fisk',
                'hund',
                'katt',
            ]
        ];
        $test->newInstance($logger, $params);

        $indata = [
            'katt' => "mjau",
            'apa'  => 123
        ];
        $expected = [
            ':katt' => "mjau",
            ':apa'  => 123,
            ':fisk' => null,
            ':hund' => null,
        ];
        $test->testThatArgs($indata)->returnsValue($expected);


        $indata = [
            'katt'     => "mjau",
            'apa'      => 123,
            'hummmer'  => 'test',
            'kanin'    => 'test',
        ];
        $expected = [
            ':katt' => "mjau",
            ':apa'  => 123,
            ':fisk' => null,
            ':hund' => null,
        ];
        $test->testThatArgs($indata)->returnsValue($expected);
    }

    public static function __constructTest($test)
    {
        /*
        * Since __costruct is rewritten here there is no point in testing it.
        */
    }

    public static function processNextTest($test)
    {
        // require_once '/var/www/magento/magento1931/magento/app/Mage.php';
        // \Mage::app();
        // $write  = \Mage::getSingleton('core/resource')->getConnection('core_write');
        // $params = [
        //     'adapter'    => $write,
        //     'table_name' => 'admin_assert',
        //     'columns'    => ['assert_id'],
        // ];
        // $data = [
        //     [
        //         'assert_id' => 11,
        //     ],
        //     [
        //         'assert_id' => 16,
        //     ],
        //     [
        //         'assert_id' => 18,
        //     ],
        // ];
        // $test->newInstance($params)
        //     ->testThatArgs($data)->returnsNull();
    }

    public static function initTest($test)
    {
        // Can not test this without an adapter.
    }

    public static function finalizeTest($test)
    {
    }

    public static function countTest($test)
    {
    }

    public static function getNextStepsTest($test)
    {
    }
}
