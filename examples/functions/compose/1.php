<?php
use Webbhuset\Pipeline\Constructor as F;

$compose = F::Compose([
    F::Map('trim'),
    F::Filter('is_numeric'),
    F::Map('intval'),
]);

$input = ['1', '  23 ', 'hello', '4.444', 5.75, '+12e3'];

echo json_encode(iterator_to_array($compose($input)));

// Output: [1,23,4,5,12000]
