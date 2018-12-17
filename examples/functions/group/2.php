<?php

use Webbhuset\Pipeline\Constructor as F;

$group = F::Group(function($value, $batch, $finalize) {
    if ($finalize) {
        return array_sum($batch) >= 10;
    }

    return array_sum($batch) < 10;
});

$input = [1, 2, 3, 4, 5, 6, 7, 8, 9];

echo json_encode(iterator_to_array($group($input)));

// Output: [[1,2,3,4],[5,6],[7,8]]
