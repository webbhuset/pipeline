<?php
use Webbhuset\Pipeline\Constructor as F;

$reduce = F::Reduce(function ($value, $carry) {
    return $carry + $value;
}, 0);

$input = [1, 4, 8, 15];

echo json_encode(iterator_to_array($reduce($input)));

// Output: [28]
