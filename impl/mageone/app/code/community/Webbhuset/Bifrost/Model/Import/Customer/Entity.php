<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Type;

class Webbhuset_Bifrost_Model_Import_Customer_Entity
{
    public function create($config)
    {
        $config = array_merge([
            'updateAttributes'      => [],
            'keys'                  => ['email'],
        ], $config);

        $type       = Mage::getSingleton('eav/config')->getEntityType('customer');
        $eavToDb    = Mage::getModel('whbifrost/service_import_eav_entity')->create($type, $config);

        $defaults = [
            'entity_type_id'    => (int)$type->getId(),
            'website_id'        => 0,
            'store_id'          => 0,
            'is_active'         => 1,
            'created_at'        => '0000-00-00 00:00:00',
            'created_in'        => 'Customer Import',
            'updated_at'        => '0000-00-00 00:00:00',
            'disable_auto_group_change' => 0,
        ];

        $pipeline = [
            new Core\Component\Transform\Map(function ($item) use ($defaults) {
                $item = array_merge($defaults, $item);

                return $item;
            }),
        ];

        return array_merge($pipeline, $eavToDb);
    }
}
