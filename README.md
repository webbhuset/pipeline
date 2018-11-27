# Whaskell

Whaskell is a collection of functions for manipulating data designed so that they can be chained together.

Every Whaskell function is a class implementing \_\_invoke(), thus allowing instances to be run as functions. Every function takes a Traversable as input and returns a Generator. Functions are most easily constructed using the static methods in the Constructor class, but can also be constructed directly using `new`.


## Functions

### Iterable

* [Expand](docs/functions/expand.md) - Yields one or more values from every input value.
* [Filter](docs/functions/filter.md) - Remove input values based on a callback.
* [Group](docs/functions/group.md) - Group input values based on a callback.
* [GroupCount](docs/functions/group-count.md) - Group input values in groups of X.
* [Map](docs/functions/map.md) - Modify every input value.
* [Observe](docs/functions/observe.md) - Send input values to a callback without modifying them.
* [Reduce](docs/functions/reduce.md) - Reduce all input values to a single value.
* [Scan](docs/functions/scan.md) - Reduce all input values, returning the intermediate results.
* [Slice](docs/functions/slice.md) - Extract a subset of the input values.

### Flow

* [Compose](docs/functions/compose.md)  - Chain functions together.
* [Defer](docs/functions/defer.md) - Delay construction of a function.
* [Factory](docs/functions/factory.md) - Construct a function for every value.
* [Fork](docs/functions/fork.md) - Send input value to multiple functions.
* [Multiplex](docs/functions/multiplex.md) - Send input value to one function based on a callback.


## Usage

### Example 1 - Basic usage
```php
<?php
use Webbhuset\Whaskell\Constructor as F; // Alias constructor for ease of use.

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
use Webbhuset\Whaskell\Constructor as F;

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
31
1230000
```
