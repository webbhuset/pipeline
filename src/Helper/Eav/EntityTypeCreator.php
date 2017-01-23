<?php

namespace Webbhuset\Bifrost\Core\Helper\Eav;

use Webbhuset\Bifrost\Core\Type;

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
}
