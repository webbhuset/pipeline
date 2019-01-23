<?php

use Webbhuset\Pipeline\Constructor as F;

$multiplex = F::Multiplex(
    function ($value) {
        return $value <= 10;
    },
    [
        true => F::Map(function ($value) {
            return $value * 2;
        }),
        false => [],
    ]
);

$input = [1, 22, 3, 44, 5];

echo json_encode(iterator_to_array($multiplex($input)));

// Output: [2,22,6,44,10]
