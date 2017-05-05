<?php

namespace Webbhuset\Bifrost\Data\Eav;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Data\Attribute as SimpleAttribute;
use Webbhuset\Bifrost\Type;

class Attribute extends SimpleAttribute implements AttributeInterface
{
    public function __construct(array $data)
    {
        parent::__construct($data);

        $requiredKeys = ['id', 'scope'];

        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new BifrostException("Constructor key '{$key}' is missing.");
            }
        }

        if (!$data['scope'] instanceof Attribute\ScopeInterface) {
            throw new BifrostException('Scope has to implement interface ' . __CLASS__ .'\\ScopeInterface');
        }

        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getScope()
    {
        return $this->data['scope'];
    }

    public function isMultiSelect()
    {
        return isset($this->data['input']) && $this->data['input'] == 'multiselect';
    }
}
