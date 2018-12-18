<?php

use Webbhuset\Pipeline\Constructor as F;

$observe = F::Observe(function($value) {
    echo $value . "\n";
});

$input = [1, 2, 3, 4, 5, 6];

iterator_to_array($observe($input));

/**
 * Output:
 *  1
 *  2
 *  3
 *  4
 *  5
 *  6
 */
