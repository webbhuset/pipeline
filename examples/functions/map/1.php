<?php
use Webbhuset\Whaskell\Constructor as F;

$map = F::Map(function($value) {
    return $value * 2;
});

$input = [1, 2, 5, 12];

echo json_encode(iterator_to_array($map($input)));

// Output: [2,4,10,24]
