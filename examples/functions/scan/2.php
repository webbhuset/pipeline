<?php
use Webbhuset\Pipeline\Constructor as F;

$function = F::Compose([
    F::Scan(function($value, $carry) {
        return $carry . $value;
    }, ''),
    F::Drop(1),
]);

$input = ['Hello', ' ', 'world', '!'];

echo json_encode(iterator_to_array($function($input)));

// Output: ["Hello","Hello ","Hello world","Hello world!"]
