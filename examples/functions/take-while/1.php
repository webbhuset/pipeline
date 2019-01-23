<?php
use Webbhuset\Pipeline\Constructor as F;

$takeWhile = F::TakeWhile(function ($value) {
    return $value < 7;
});

$input = [1, 3, 5, 7, 9, 2, 4, 6, 8];

echo json_encode(iterator_to_array($takeWhile($input)));

// Output: [1,3,5]
