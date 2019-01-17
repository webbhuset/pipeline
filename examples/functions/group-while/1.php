<?php

use Webbhuset\Pipeline\Constructor as F;

$group = F::GroupWhile(function($value, $batch) {
    return !$batch                  // Add to batch if empty
        || $value == reset($batch); // Add if value is the same as values in batch
});

$input = [1, 1, 1, 2, 3, 3, 1, 2, 2];

echo json_encode(iterator_to_array($group($input)));

// Output: [[1,1,1],[2],[3,3],[1],[2,2]]
