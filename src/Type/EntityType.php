<?php
namespace Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\BifrostException;

class EntityType extends StructType
{
    protected $entityId;

    public function __construct($params)
    {
        parent::__construct($params);
        if (!isset($params['entity_id_field'])) {
            throw new BifrostException("Entity id field must be set");
        }

        $this->entityId = $params['entity_id_field'];
    }

    public function diff($old, $new) {
        $result = parent::diff($old, $new);
        if (!empty($result) && !isset($result[$this->entityId]) && isset($new[$this->entityId])) {
            $result[$this->entityId]['+'] = $new[$this->entityId];
            $result[$this->entityId]['-'] = $new[$this->entityId];
        }
        if (count($result)===1 && isset($result[$this->entityId])) {
            return [];
        }

        return $result;
    }
}
