<?php
use Webbhuset\Pipeline\Constructor as F;

$chunk = F::Chunk(3);

$input = [1, 2, [3, 4], 'five', 6, null, 8];

echo json_encode(iterator_to_array($chunk($input)));

// Output: [[1,2,[3,4]],['five', 6, null],[8]]
