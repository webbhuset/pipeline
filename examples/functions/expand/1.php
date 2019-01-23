<?php

use Webbhuset\Pipeline\Constructor as F;

$expand = F::Expand();

$input = [[1, 2, 3], [4, 5, 6]];

echo json_encode(iterator_to_array($expand($input)));

// Output: [1,2,3,4,5,6]
