# Pipeline

Pipeline is a collection of functions for manipulating data designed so that they can be chained together.

Every Pipeline function is a class implementing \_\_invoke(), thus allowing instances to be run as functions.
Every function takes a Traversable as input and returns a Generator.
Functions are most easily constructed using the static methods in the Constructor class, but can also be constructed directly using `new`.


## Functions

### Value

* [Chunk](docs/functions/chunk.md) - Group input values in groups of a specified size.
* [Drop](docs/functions/drop.md) - Discard the first N input values and return the rest.
* [DropWhile](docs/functions/drop-while.md) - Discard input values while callback returns true.
* [Expand](docs/functions/expand.md) - Yields one or more values from every input value.
* [Filter](docs/functions/filter.md) - Remove input values based on a callback.
* [Group](docs/functions/group.md) - Group input values based on a callback.
* [Map](docs/functions/map.md) - Modify every input value with a callback.
* [Observe](docs/functions/observe.md) - Send input values to a callback without modifying them.
* [Reduce](docs/functions/reduce.md) - Reduce all input values to a single value.
* [Scan](docs/functions/scan.md) - Reduce all input values, returning the intermediate results.
* [Take](docs/functions/take.md) - Return the first N input values and discard the rest.
* [TakeEvery](docs/functions/take-every.md) - Return every N<sup>th</sup> input value.
* [TakeWhile](docs/functions/take-while.md) - Return input values while callback returns true.


### Flow

* [Compose](docs/functions/compose.md)  - Chain functions together.
* [Defer](docs/functions/defer.md) - Delay construction of a function.
* [Factory](docs/functions/factory.md) - Construct a function for every input value.
* [Fork](docs/functions/fork.md) - Send every input value to multiple functions.
* [Multiplex](docs/functions/multiplex.md) - Send every input value to one function based on a callback.


## Usage

### Example 1 - Basic usage
```php
<?php
use Webbhuset\Pipeline\Constructor as F; // Alias constructor for ease of use.

$function = F::Map(function($value) {
    $value *= 2;

    return $value;
});

$generator = $function([1, 2, 5, 12]);
foreach ($generator as $value) {
    echo $value . "\n";
}
```

Output:
```
2
4
10
24
```


### Example 2 - Composing functions
```php
<?php
use Webbhuset\Pipeline\Constructor as F;

$function = F::Compose([
    F::Map('trim'),
    F::Filter('is_numeric'),
    F::Map('intval'),
]);

$generator = $function(['1', '  23 ', 'hello', '4.444', 5.75, '+12e3']);
foreach ($generator as $value) {
    echo $value . "\n";
}
```

Output:
```
1
23
4
5
12000
```


### Example 3 - Import to a database from a file, logging invalid rows
```php
<?php
use Webbhuset\Pipeline\Constructor as F;

$insertToDb = F::Observe(function($row) {
    DbConnection::insert($row);
});

$logErrors = F::Observe(function($row) {
    $msg = sprintf(
        'Invalid row: %s',
        json_encode($row)
    );
    Logger::writeLog($msg);
});

$function = F::Compose([
    F::Expand(function($file) {
        $file = fopen($file);

        while ($row = fgetcsv($file)) {
            yield $row;
        }
    }),
    F::Multiplex(
        function($row) {
            return count($row) == 4 ? 'insert' : 'log';
        },
        [
            'insert' => $insertToDb,
            'log' => $logErrors,
        ]
    ),
]);

$generator = $function(['data.csv']);
iterator_to_array($generator);
```
