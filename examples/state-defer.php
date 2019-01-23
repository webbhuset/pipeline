<?php
use Webbhuset\Pipeline\Constructor as F;

$take = F::Take(5);

$takeWithDefer = F::Defer(function () {
    return F::Take(5);
});

$input1 = range(0, 9);
$input2 = range(10, 19);

function loop($gen1, $gen2) {
    while ($gen1->valid() || $gen2->valid()) {
        if ($gen1->valid()) {
            echo 'Gen1: ' . $gen1->current() . "\n";
            $gen1->next();
        }
        if ($gen2->valid()) {
            echo 'Gen2: ' . $gen2->current() . "\n";
            $gen2->next();
        }
    }
}

$gen1 = $take($input1);
$gen2 = $take($input2);

echo "Without Defer, unexpected results:\n";
loop($gen1, $gen2);

$gen1 = $takeWithDefer($input1);
$gen2 = $takeWithDefer($input2);

echo "With Defer, expected results:\n";
loop($gen1, $gen2);

/**
 * Output:
 *  Without Defer, unexpected results:
 *  Gen1: 0
 *  Gen2: 10
 *  Gen1: 1
 *  Gen2: 11
 *  Gen1: 2
 *  Gen1: 3
 *  Gen1: 4
 *  Gen1: 5
 *  Gen1: 6
 *  Gen1: 7
 *  With Defer, expected results:
 *  Gen1: 0
 *  Gen2: 10
 *  Gen1: 1
 *  Gen2: 11
 *  Gen1: 2
 *  Gen2: 12
 *  Gen1: 3
 *  Gen2: 13
 *  Gen1: 4
 *  Gen2: 14
 */
