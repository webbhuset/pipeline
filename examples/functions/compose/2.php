<?php
use Webbhuset\Pipeline\Constructor as F;

function getMapFunction()
{
    return [
        F::Map('trim'),
        F::Map('ucwords'),
    ];
}

$compose = F::Compose([
    getMapFunction(),
    F::Compose([
        F::Filter('is_numeric')
    ]),
    [
        [
            F::Map('intval'),
        ],
    ],
]);

/**
 * Result function would look like this:
 *  [
 *       F::Map('trim'),
 *       F::Map('ucwords'),
 *       F::Filter('is_numeric'),
 *       F::Map('intval'),
 *  ]
 */
