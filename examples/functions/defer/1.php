<?php
use Webbhuset\Pipeline\Constructor as F;

$defer = F::Defer(function () {
    sleep(1); // Sleep to simulate fetching IDs from a database.
    $idMap = [
        'alpha' => 1,
        'beta'  => 2,
        'gamma' => 3,
    ];

    return F::Map(function ($value) use ($idMap) {
        return $idMap[$value] ?? null;
    });
});

$input = ['alpha', 'gamma', 'omega'];

echo json_encode(iterator_to_array($defer($input)));

// Output: [1,3,null]
