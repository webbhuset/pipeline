<?php

namespace Webbhuset\Bifrost\Core\Helper\Eav;

use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\TypeConstructor as T;

class EntityTypeCreator
{
    public static function createFromAttributes($attributes)
    {
        $fields = [];
        foreach ($attributes as $attribute) {
            $code           = $attribute->getCode();
            $typeObject     = $attribute->getTypeObject();
            $fields[$code]  = $typeObject;
        }

        $structType = new Type\StructType(['fields' => $fields]);

        return $structType;
    }

    public static function createFromSets($sets)
    {
        $entityTypes = [];
        foreach ($sets as $set) {
            $id                 = $set->getId();
            $entityTypes[$id]   = self::createFromAttributes($set->getAttributes());
        }

        return $entityTypes;
    }
}
