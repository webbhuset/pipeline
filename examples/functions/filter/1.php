<?php
use Webbhuset\Pipeline\Constructor as F;

$filter = F::Filter(function($value) {
    return $value % 2 == 0;
});

$input = [1, 2, 3, 4, 5, 6, 7, 8];

echo json_encode(iterator_to_array($filter($input)));

// Output: [2,4,6,8]
