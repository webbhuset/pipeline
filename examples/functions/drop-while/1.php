<?php
use Webbhuset\Pipeline\Constructor as F;

$dropWhile = F::DropWhile(function ($value) {
    return $value < 7;
});

$input = [1, 3, 5, 7, 9, 2, 4, 6, 8];

echo json_encode(iterator_to_array($dropWhile($input)));

// Output: [7,9,2,4,6,8]
