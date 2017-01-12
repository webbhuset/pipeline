<?php
require_once 'Webbhuset/Bifrost/Autoload.php';

class Webbhuset_Bifrost_Model_Test extends Mage_Core_Model_Abstract
{
    public function test()
    {
        $stringType = new Webbhuset\Bifrost\Core\Utils\Type\StringType;
        dahbug::dump($stringType);
    }
}
