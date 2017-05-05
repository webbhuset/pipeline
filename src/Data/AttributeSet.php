<?php

namespace Webbhuset\Bifrost\Data;

use Webbhuset\Bifrost\BifrostException;

class AttributeSet implements AttributeSetInterface
{
    protected $data;

    public function __construct(array $data)
    {
        $requiredKeys = ['id', 'name', 'attributes'];

        foreach ($requiredKeys as $key) {
            if (empty($data[$key])) {
                throw new BifrostException("Constructor key '{$key}' is empty.");
            }
        }

        foreach ($data['attributes'] as $attribute) {
            if (!$attribute instanceof AttributeInterface) {
                throw new BifrostException("Attributes must implement 'Webbhuset\Bifrost\Data\AttributeInterface'.");
            }
            $attributes[$attribute->getCode()] = $attribute;
        }
        $data['attributes'] = $attributes;

        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getAttributes()
    {
        return $this->data['attributes'];
    }
}
