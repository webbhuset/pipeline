<?php

namespace Webbhuset\Bifrost\Component\Sequence\Import\Eav\Entity;

interface ActionsInterface
{
    public function getEntityIds($entities);
    public function createEntities($entities);
    public function insertAttributeValues($rows, $type);
    public function fetchAttributeValues($entityIds, $type, $attributes);
}
