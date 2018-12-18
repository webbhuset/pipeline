<?php

use Webbhuset\Pipeline\Constructor as F;

$multiplex = F::Multiplex(
    function($value) {
        return $value % 2 == 0 ? 'even' : 'odd';
    },
    [
        'even' => F::Map(function($value) {
            return $value / 2;
        }),
        'odd' => F::Map(function($value) {
            return $value * 2;
        }),
    ]
);

$input = [1, 2, 3, 4, 5, 6];

echo json_encode(iterator_to_array($multiplex($input)));

// Output: [2,1,6,2,10,3]
