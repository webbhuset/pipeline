<?php
use Webbhuset\Pipeline\Constructor as F;

$take = F::Take(2);

$input = [1, 3, 5, 7, 9];

echo json_encode(iterator_to_array($take($input)));

// Output: [1,3]
