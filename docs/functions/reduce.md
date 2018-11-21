# Reduce

```php
Generator Reduce(callable $callback, mixed $initialValue = [])
```

Reduce all input values into a single value.


## Parameters

### callback

```php
mixed callback (mixed $value, mixed $carry)
```
Description

* value - The current value.
* carry - The return value of previous iteration.

### intialValue

The initial value of $carry.


## Examples

```php
<?php
F::Reduce(function($value, $carry) {
    $carry += $value['qty'];

    return $carry;
}, 0);
```
