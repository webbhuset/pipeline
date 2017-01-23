<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\Helper;

class Webbhuset_Bifrost_Model_Service_Import_Eav_Entity
{
    public function create(Mage_Eav_Model_Entity_Type $type, $config)
    {
        $eavHelper  = Mage::helper('whbifrost/eav');
        $attributes = $eavHelper->getAttributes($type, $config);
        $sets       = $eavHelper->getAttributeSets($type, $attributes);

        $mapOptions = $this->getComponentOptionsMapper($attributes);
        $validate   = $this->getComponentValidate($attributes);
        $mapTree    = $this->getComponentMapTree($attributes);
        $sequence   = $this->getComponentEavSequence($attributes, $sets, $type);
        $monad      = $this->getComponentMonad($type, $config, $attributes);

        return [$mapOptions, $validate, $mapTree, $sequence, $monad];
    }

    public function getComponentOptionsMapper($attributes)
    {
        $optionAttributes = [];

        foreach ($attributes as $attribute) {
            if ($attribute->usesOptions()) {
                $optionAttributes[] = $attribute;
            }
        }

        $mapOptions = new Component\Transform\Map(function($item) use ($optionAttributes) {
            foreach ($optionAttributes as $attribute) {
                $code = $attribute->getCode();
                if (!isset($item[$code])) {
                    continue;
                }

                $value       = $item[$code];
                $item[$code] = $attribute->mapOptionValue($value);
            }
            return $item;
        });

        return $mapOptions;
    }

    public function getComponentValidate($attributes)
    {
        $entityType = Helper\Eav\EntityTypeCreator::createFromAttributes($attributes);
        $validate   = new Component\Validate\Entity($entityType);

        return $validate;
    }

    public function getComponentMapTree($attributes)
    {
        $usesScope      = $this->usesScope($attributes);
        $attributeMap   = [];
        $scopes         = [];
        $globalScope    = new Helper\ArrayHelper\KeyMapper([], 0);

        foreach ($attributes as $attribute) {
            if ($attribute->isStatic()) {
                continue;
            }
            $code                = $attribute->getCode();
            $id                  = $attribute->getId();
            $attributeMap[$code] = $id;

            if ($usesScope) {
                $scopes[$code] = $globalScope;
            }
        }
        $atCodeToId = new Helper\ArrayHelper\KeyMapper($attributeMap);
        $treeMapper = Helper\ArrayHelper\Tree::buildRecursiveMapper($atCodeToId, $scopes);

        return new Component\Transform\Map(function($item) use ($treeMapper) {
            return $treeMapper->map($item);
        });
    }

    public function getComponentEavSequence($attributes, $sets, $type)
    {
        $attributesByTable = [];

        foreach ($attributes as $attribute) {
            $backendType    = $attribute->getBackendType();
            if ($backendType == 'static') {
                continue;
            }
            $table          = $attribute->getTable();
            $id             = $attribute->getId();

            $attributesByTable[$table][] = $id;
        }

        $attributeSetsByName = [];

        foreach ($sets as $set) {
            $name = $set->getId();
            $attributeSetsByName[$name] = $set->getAttributesIds();
        }
        $valueTableColumns  = $this->getValueTableColumns($attributes);
        $usesScope          = $this->usesScope($attributes);

        if ($usesScope) {
            $dimensions = ['entity_id', 'attribute_id', 'store_id', 'value'];
        } else {
            $dimensions = ['entity_id', 'attribute_id', 'value'];
        }

        $config = [
            'attributesByType'      => $attributesByTable,
            'attributeSetsByName'   => $attributeSetsByName,
            'valueTableConfig'      => [
                'columns'       => $valueTableColumns,
                'dimensions'    => $dimensions,
                'static'        => ['value_id' => null, 'entity_type_id' => (int)$type->getId()]
            ],
            'defaultScope' => $usesScope ? 0 : null,
        ];

        return new Component\Sequence\Import\Eav\Entity($config);
    }

    protected function getValueTableColumns($attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->getBackendType() != 'static') {
                break;
            }
        }
        $columns = array_keys(Mage::helper('whbifrost/eav')->getTableColumns($attribute->getTable()));

        return $columns;
    }

    public function getComponentMonad($type, $config, $attributes)
    {
        $entityTableName    = $type->getResource()->getTable($type->getEntityTable());
        $entityTableColumns = Mage::helper('whbifrost/eav')->getTableColumns($type->getEntityTable());
        $entityTableColumns = array_fill_keys(array_keys($entityTableColumns), null);

        $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

        $idQueryBuilder       = new Helper\Eav\QueryBuilder\IdsFromEntities(
            $config['keys'],
            $attributes,
            $adapter,
            $this->getValueTableColumns($attributes)
        );

        $config = [
            'entityTableName'       => $entityTableName,
            'entityTableColumns'    => $entityTableColumns,
            'adapter'               => $adapter,
            'afterCreateCallback'   => $config['afterCreateCallback'],
            'idQueryBuilder'        => $idQueryBuilder,

        ];

        $handler    = Mage::getModel('whbifrost/resource_import_eav_entityActions', $config);
        $monad      = new Component\Monad\Import\Eav\Entity($handler);

        return $monad;
    }

    protected function usesScope($attributes)
    {
        $valueTableColumns = $this->getValueTableColumns($attributes);

        if (in_array('store_id', $valueTableColumns)) {
            return true;
        }

        return false;
    }
}
