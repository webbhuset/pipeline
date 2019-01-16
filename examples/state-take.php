<?php
use Webbhuset\Pipeline\Constructor as F;

$take = F::Take(3);

echo json_encode(iterator_to_array($take([1,2,3,4], true)));
echo "\n";
echo json_encode(iterator_to_array($take([5,6,7,8], true)));

/**
 * Output:
 *  [1,2,3]
 *  []
 */
