<?php

namespace Webbhuset\Bifrost\Core\Data\Eav;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Type;

class Attribute implements AttributeInterface
{
    protected $data;
    protected $typeObject;
    protected $staticType       = 'static';
    protected $optionsValueMap  = [];

    public function __construct(array $data)
    {
        $requiredKeys = ['id', 'code', 'backendType', 'scope', 'table'];

        foreach ($requiredKeys as $key) {
            if (empty($data[$key])) {
                throw new BifrostException("Constructor key '{$key}' is empty.");
            }
        }

        if (!$data['scope'] instanceof Attribute\ScopeInterface) {
            throw new BifrostException('Scope has to implement interface ' . __CLASS__ .'\\ScopeInterface');
        }

        $this->data = $data;

        $data['shouldUpdate']   = empty($data['shouldUpdate'])
                                ? false
                                : true;

        if (isset($data['typeObject']) && $data['typeObject'] instanceof Type\TypeInterface) {
            $this->typeObject = $data['typeObject'];
        }

        $this->required = !empty($data['required']);

        if (isset($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $key => $value) {
                $this->optionsValueMap[mb_strtoupper($value)] = $this->getTypeObject()->cast($key);
            }
        }
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getCode()
    {
        return $this->data['code'];
    }

    public function getTable()
    {
        return $this->data['table'];
    }

    public function getTypeObject()
    {
        if (!$this->typeObject) {
            $config['required'] = $this->required;
            switch ($this->data['backendType']) {
                case 'varchar':
                    $config['max_length'] = 255;
                    $this->typeObject = new Type\StringType($config);
                    break;

                case 'text':
                    $this->typeObject = new Type\StringType($config);
                    break;

                case 'int':
                    $this->typeObject = new Type\IntType($config);
                    break;

                case 'decimal':
                    $this->typeObject = new Type\FloatType\DecimalType($config);
                    break;

                case 'datetime':
                    $this->typeObject = new Type\StringType\DatetimeType($config);
                    break;

                default:
                    $this->typeObject = new Type\StringType($config);
                    break;
            }
        }

        return $this->typeObject;
    }

    public function getBackendType()
    {
        return $this->data['backendType'];
    }

    public function getScope()
    {
        return $this->data['scope'];
    }

    public function shouldUpdate()
    {
        return $this->data['shouldUpdate'];
    }

    public function usesOptions()
    {
        return !is_null($this->data['options']);
    }

    public function isMultiSelect()
    {
        return isset($this->data['input']) && $this->data['input'] == 'multiselect';
    }

    public function mapOptionValue($value)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->mapOptionValue($v);
            }

            return $value;
        }

        $key = mb_strtoupper($value);

        if (isset($this->optionsValueMap[$key])) {
            return $this->optionsValueMap[$key];
        }

        return $value;
    }

    public function isEav()
    {
        return $this->getBackendType() != $this->staticType;
    }

    public function isStatic()
    {
        return $this->getBackendType() == $this->staticType;
    }
}
