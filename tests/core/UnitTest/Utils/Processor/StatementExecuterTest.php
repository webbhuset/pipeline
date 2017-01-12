<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;

class StatementExecuterTest
{

    public static function __constructTest($test)
    {
    }

    public static function processNextTest($test)
    {
        require_once '/var/www/magento/magento1931/magento/app/Mage.php';
        \Mage::app();
        $nullLogger    = new NullLogger;
        $collector     = new Collector;
        $mockProcessor = [$collector];
        $write         = \Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection    = $write->getConnection();
        $statement     = $connection->prepare("SELECT * FROM catalog_product_entity where SKU like :sku");
        $params        = [
            'statement'  => $statement,
            'connection' => $connection,
        ];
        $indata = [
            'bind_values' =>[':sku' => '123123'],
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsNull();

        $result = $collector->getData();
    }

    public static function finalizeTest()
    {
    }
    public static function initTest()
    {
    }
    public static function countTest()
    {
    }
}
