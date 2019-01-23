<?php
use Webbhuset\Pipeline\Constructor as F;

$fork = F::Fork([
    F::Map(function ($value) {
        return $value * 2;
    }),
    F::Map(function ($value) {
        return str_repeat('a', $value);
    }),
]);

$input = [1, 2, 3, 4, 5];

echo json_encode(iterator_to_array($fork($input)));

// Output: [2,"a",4,"aa",6,"aaa",8,"aaaa",10,"aaaaa"]
