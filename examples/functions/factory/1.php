<?php
use Webbhuset\Pipeline\Constructor as F;

$factory = F::Factory(function($value) {
    return F::Map('trim');
});

$input = ['1  ', '   ', '  foo  ', "bar\n"];

echo json_encode(iterator_to_array($factory($input)));

// Output: ["1","","foo","bar"]
