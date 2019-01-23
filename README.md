# Pipeline

[![Documentation Status](https://readthedocs.org/projects/php-pipeline/badge/?version=latest)](https://php-pipeline.readthedocs.io/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

Pipeline is a PHP library for building reusable functions for manipulating values. Every Pipeline
function is a class implementing \_\_invoke(), thus allowing instances to be run as functions. Every
function takes a Traversable as input and returns a Generator.


## Documentation

Documentation is available at [ReadTheDocs](https://php-pipeline.readthedocs.io/).


## Example

```php
<?php

use Webbhuset\Pipeline\Constructor as F;

$fun = F::Compose([
    F::Map('trim'),
    F::Filter('is_numeric'),
    F::Map('intval'),
    F::Drop(2),
    F::Multiplex(
        function ($value) {
            return $value % 10 == 0 ? 'divide' : 'double';
        },
        [
            'divide' => F::Map(function ($value) {
                return $value / 10;
            }),
            'double' => F::Map(function ($value) {
                return $value * 2;
            }),
        ]
    )
]);

$input = [
    1,
    '  23 ',
    'hello',
    '4.444',
    5.75,
    '+12e3'
];

echo json_encode(iterator_to_array($fun($input)));

// Output: [8,10,1200]
```
