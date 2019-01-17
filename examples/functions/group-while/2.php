<?php

use Webbhuset\Pipeline\Constructor as F;

$fun = F::Compose([
    F::GroupWhile(function ($value, $batch) {
        return array_sum($batch) < 10;
    }),
    F::Filter(function ($values) {
        return array_sum($values) >= 10;
    })
]);

$input = [1, 2, 3, 4, 5, 6, 7, 8, 9];

echo json_encode(iterator_to_array($fun($input)));

// Output: [[1,2,3,4],[5,6],[7,8]]
