<?php
Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\Helper;

class Webbhuset_Bifrost_Helper_Eav
{
    public function getEntityType($type)
    {
    }

    public function getAttributes($type, $config)
    {
        $config             = new Helper\ArrayHelper\DataAccess($config);
        $eavAttributes      = $this->getEavAttributes($type, $config);
        $staticAttributes   = $this->getEntityColumnsAttribute($type, $config);

        return array_replace($staticAttributes, $eavAttributes);
    }

    public function getEavAttributes($type, $config)
    {
        $attributes     = [];
        $collection     = $type->getAttributeCollection();

        foreach ($collection as $attribute) {
            if ($attribute->getBackendType() == 'static') {
                continue;
            }
            $code       = $attribute->getAttributeCode();
            $typeObject = $config[['types', $code]];

            $options = null;

            if ($attribute->usesSource()) {
                $options = $this->getOptionsMap($attribute->getSource());
            }

            $attributes[$code] = new Data\Eav\Attribute([
                'id'            => (int)$attribute->getAttributeId(),
                'code'          => $code,
                'backendType'   => $attribute->getBackendType(),
                'scope'         => $this->getAttributeScope($attribute),
                'table'         => $attribute->getBackendTable(),
                'shouldUpdate'  => $this->shouldUpdateAttribute($code, $config['updateAttributes']),
                'required'      => (bool)$attribute->getIsRequired(),
                'typeObject'    => $typeObject,
                'defaultValue'  => $attribute->getDefaultValue(),
                'options'       => $options,
            ]);
        }

        return $attributes;
    }

    public function getEntityColumnsAttribute($type, $config)
    {
        $columns        = $this->getTableColumns($type->getEntityTable());
        $entityTable    = $type->getResource()->getTable($type->getEntityTable());
        $sets           = $type->getAttributeSetCollection()->toOptionHash();
        $collection     = $type->getAttributeCollection();

        foreach ($columns as $name => $def) {
            $typeObject = $config[['types', $name]];
            $attribute = $collection->getItemsByColumnValue('attribute_code', $name);

            if (!$typeObject) {
                $typeObject = $this->getTypeObjectFromColumnDefinition($def);
            }

            $options = null;

            if (count($attribute)) {
                $attribute = reset($attribute);
                if ($attribute->usesSource()) {
                    $options = $this->getOptionsMap($attribute->getSource());
                }
            }

            if ($name == 'attribute_set_id') {
                $options = $sets;
            }


            $attributes[$name] = new Data\Eav\Attribute([
                'id'            => 1,
                'code'          => $name,
                'backendType'   => 'static',
                'scope'         => new Data\Eav\Attribute\Scope([]),
                'table'         => $entityTable,
                'shouldUpdate'  => false,
                'typeObject'    => $typeObject,
                'options'       => $options,
                'defaultValue'  => $def['DEFAULT'],
            ]);
        }

        return $attributes;
    }

    public function getAttributeSets($type, $attributes)
    {
        $attributeSets  = [];
        $adapter        = $type->getResource()->getReadConnection();
        $setIdToName    = [];

        foreach ($type->getAttributeSetCollection() as $set) {
            $id                 = (int)$set->getAttributeSetId();
            $name               = $set->getAttributeSetName();
            $setIdToName[$id]   = $name;

            $attributeSets[$name] = [
                'id'            => $id,
                'name'          => $name,
                'attributes'    => [],
            ];
        }
        $entityAttributes   = new Varien_Data_Collection_Db($adapter);
        $eeaTable           = $type->getResource()->getTable('eav/entity_attribute');
        $eaTable            = $type->getResource()->getTable('eav/attribute');

        $select = $entityAttributes->getSelect()
            ->from(['eea' => $eeaTable])
            ->join(
                ['ea' => $eaTable],
                'ea.attribute_id = eea.attribute_id',
                ['ea.attribute_code']
            )
            ->where('eea.entity_type_id = ?', $type->getEntityTypeId())
            ->order('eea.attribute_set_id ASC');

        foreach ($entityAttributes as $entityAttribute) {
            $id         = (int)$entityAttribute->getAttributeId();
            $setId      = (int)$entityAttribute->getAttributeSetId();
            $code       = $entityAttribute->getAttributeCode();
            $setName    = $setIdToName[$setId];

            if (!isset($attributes[$code])) {
                continue;
            }
            $attribute = $attributes[$code];
            if ($attribute->getBackendType() == 'static') {
                continue;
            }

            $attributeSets[$setName]['attributes'][$code] = $attributes[$code];
        }

        $sets = [];

        foreach ($attributeSets as $set) {
            $sets[] = new Data\Eav\AttributeSet($set);
        }

        return $sets;
    }

    protected function getAttributeScope($attribute)
    {
        return new Data\Eav\Attribute\Scope([]);
    }

    protected function shouldUpdateAttribute($code, $updateAttributes)
    {
        return in_array($code, (array)$updateAttributes);
    }

    public function getOptionsMap($source)
    {
        $allOptions = $source->getAllOptions();
        $options    = [];

        foreach ($allOptions as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }

    public function getTableColumns($table)
    {
        $resource  = Mage::getSingleton('core/resource');
        $adapter   = $resource->getConnection('core_read');
        $tableName = $resource->getTableName($table);
        $columns   = $adapter->describeTable($tableName);

        return $columns;
    }
    protected function getTypeObjectFromColumnDefinition($def)
    {
        $type       = $def['DATA_TYPE'];
        $unsigned   = $def['UNSIGNED'];
        $nullable   = $def['NULLABLE'];
        $primary    = $def['PRIMARY'];
        $length     = $def['LENGTH'];
        $typeObject = null;

        switch ($type) {
            case 'int':
            case 'smallint':
            case 'tinyint':
                $config = [
                    'required' => !$nullable && !$primary,
                ];
                if ($unsigned) {
                    $config['min_value'] = 0;
                }
                $typeObject = new Type\IntType($config);
                break;

            case 'varchar':
                $config = [
                    'required' => !$nullable && !$primary,
                ];
                if ($length) {
                    $config['max_length'] = (int)$length;
                }
                $typeObject = new Type\StringType($config);
                break;

            case 'timestamp':
                $config = [
                    'required' => !$nullable && !$primary,
                ];
                $typeObject = new Type\StringType\DatetimeType($config);
                break;
        }

        return $typeObject;
    }
}
