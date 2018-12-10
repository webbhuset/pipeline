<?php
use Webbhuset\Pipeline\Constructor as F;

$defer = F::Defer(function() {
    return F::Map('trim');
});

$input = ['1  ', '   ', '  foo  ', "bar\n"];

echo json_encode(iterator_to_array($defer($input)));

// Output: ["1","","foo","bar"]
