<?php

namespace Webbhuset\Bifrost\Core\Component\Sequence\Import\Table\Flat;

interface ActionsInterface
{
    public function getOldData(array $rows);
    public function insertNewRows(array $rows);
    public function updateRows(array $rows, array $updateColumns);
}
