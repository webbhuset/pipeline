<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core;

class Webbhuset_Bifrost_Model_Import_Customer_Address
{
    public function create($config)
    {
        /**
         * @TODO Implement....
         */
        $config = array_merge([
            'updateAttributes'      => [],
            'keys'                  => ['customer_email'],
        ], $config);

        $type       = Mage::getSingleton('eav/config')->getEntityType('customer_address');
        $eavToDb    = Mage::getModel('whbifrost/service_eav_entityToDb')->create($type, $config);

        return $eavToDb;
    }
}
