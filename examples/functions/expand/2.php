<?php

use Webbhuset\Pipeline\Constructor as F;

$expand = F::Expand(function($value) {
    foreach ($value['foos'] as $foo) {
        foreach ($value['bars'] as $bar) {
            yield [
                'foo' => $foo,
                'bar' => $bar,
            ];
        }
    }
});

$input = [
    [
        'foos' => [1, 2, 3],
        'bars' => [4, 5],
    ]
];

echo json_encode(iterator_to_array($expand($input)), JSON_PRETTY_PRINT);

/**
 * Output:
 *  [
 *      {
 *          "foo": 1,
 *          "bar": 4
 *      },
 *      {
 *          "foo": 1,
 *          "bar": 5
 *      },
 *      {
 *          "foo": 2,
 *          "bar": 4
 *      },
 *      {
 *          "foo": 2,
 *          "bar": 5
 *      },
 *      {
 *          "foo": 3,
 *          "bar": 4
 *      },
 *      {
 *          "foo": 3,
 *          "bar": 5
 *      }
 *  ]
 */
