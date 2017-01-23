<?php

namespace Webbhuset\Bifrost\Core\Data\Eav;

use Webbhuset\Bifrost\Core\BifrostException;

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

    public function getAttributesIds()
    {
        $ids = [];
        foreach ($this->data['attributes'] as $attribute) {
            $ids[] = $attribute->getId();
        }

        return $ids;
    }
}
