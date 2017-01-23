<?php

namespace Webbhuset\Bifrost\Core\Data\Eav\Attribute;

class Scope implements ScopeInterface
{
    protected $map;
    protected $defaultScopeId = 0;

    public function __construct($map)
    {
        $this->map = $map;
    }

    public function map($values)
    {
        if (!is_array($values)) {
            return [$this->defaultScopeId => $values];
        }

        if (empty($this->map)) {
            return [$this->defaultScopeId => reset($values)];
        }

        $result = [];

        foreach ($values as $scope => $value) {
            if (!isset($this->map[$scope])) {
                continue;
            }

            foreach ($this->map[$scope] as $scopeId) {
                $result[$scopeId] = $value;
            }
        }
        ksort($result);

        return $result;
    }
}
