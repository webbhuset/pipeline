<?php
use Webbhuset\Pipeline\Constructor as F;

$drop = F::Drop(2);

$input = [1, 3, 5, 7, 9];

echo json_encode(iterator_to_array($drop($input)));

// Output: [5,7,9]
