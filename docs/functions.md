# Value Functions

## Expand

Yields item(s) from every input item.

```php
<?php
F::Expand(function($item) {
    foreach ($item['children'] as $child) {
        yield $child;
    }
});
```


## Filter

Filters items based on a callback.

```php
<?php
F::Filter(function($item) {
    return $item['is_active'];
});
```


## Group

Collects items until a condition has been met (x amount of items have been collected, or a callable returns true), then returns all of them in an array.

```php
<?php
// Group by amount
F::Group(500);

// Group by callable
F::Group(function($batch, $item, $finalize) {
    if ($finalize) {
        return true;
    }
    $first = reset($batch);

    return $first['group'] != $item['group'];
});
```


## Map

```php
Generator Map(callable $callback)
```

Edits every input value.

### Parameters

#### callback
```php
mixed callback (mixed $value)
```

* value - The value being mapped.

### Examples

```php
<?php
F::Map(function($value) {
    $value['c'] = $value['a'] . $value['b'];

    return $value;
});
```


## Merge

Sends every value through a sub-function and merges result with original value. The sub-function must return the same amount of values as input.

```php
<?php
F::Merge([
    F::Map(function($value) {
        return $value['id'];
    })
    F::Group(500),
    F::DispatchSideEffect('fetchNamesFromDatabase'),
    F::Expand(function($values) {
        foreach ($values as $value) {
            yield ['name' => $value];
        }
    }),
]);
```


## Reduce

```php
Generator Reduce(callable $callback, mixed $initialValue = [])
```

Reduce all input values into a single value.


### Parameters

#### callback

```php
mixed callback (mixed $value, mixed $carry)
```
Description

* value - The current value.
* carry - The return value of previous iteration.

#### intialValue

The initial value of $carry.


### Examples

```php
<?php
F::Reduce(function($value, $carry) {
    $carry += $value['qty'];

    return $carry;
}, 0);
```


## Slice

Takes a slice of the input, e.g. only first 5 items.


# Flow Functions

## Compose

Sends the input to each inner function sequentially after each other, chaining them together. Output is the output of the last function in the chain.

```php
<?php
F::Compose([
    F::ReadLine(...),
    F::Map(...),
]);
```


## Defer
Delays construction of the inner function until execution. Input is sent to inner function, and output is the output of the inner function.

```php
<?php
F::Defer(function() {
    return F::Map(...);
});
```


## Factory
Like Defer, but the inner function is reconstructed for every item.


## Fork

Sends the input to every inner function. Output is the output of every inner function, and is generally not used.

```php
<?php
F::Fork([
    F::Compose([
        F::ReadLine(),
        F::Map(...)
    ]),
    F::Compose([
        F::ReadLine(),
        F::Map(...)
    ]),
]);
```


## Multiplex

Sends the input to one of the inner function based on a callback. Output is the output of the inner function.

```php
<?php
F::Multiplex(
    function($item) {
        return $item ? 'a' : 'b';
    },
    [
        'a' => F::Compose(...),
        'b' => F::Compose(...),
    ]
);
```
