<?php
use Webbhuset\Whaskell\Constructor as F;

$scan = F::Scan(function($value, $carry) {
    return $carry + $value;
}, 0);

$input = [1, 4, 8, 15];

echo json_encode(iterator_to_array($scan($input)));

// Output: [0,1,5,13,28]
