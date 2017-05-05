<?php

namespace Webbhuset\Bifrost\Helper\Eav;

use Webbhuset\Bifrost\Type;

class EntityTypeCreator
{
    public static function createFromAttributes($attributes)
    {
        $fields = [];
        foreach ($attributes as $attribute) {
            $code           = $attribute->getCode();
            $type           = $attribute->getType();
            $fields[$code]  = $type;
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
