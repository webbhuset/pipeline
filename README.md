# Whaskell

Whaskell is a collection of functions for manipulating data designed so that they can be chained together.

## Functions

Every Whaskell function is a class implementing \_\_invoke(), thus allowing instances to be run as a function. Every function takes a Traversable as input and returns a Generator. Functions are constructed using the static methods in the Constructor class.  
Example usage (opens a file, appends 'hello' to every line, then writes to another file):

```php
<?php
use Webbhuset\Whaskell\Constructor as F; // Alias constructor for ease of use.

$function = F::Compose([
    F::ReadLine(),
    F::Map(function($item) {
        $item .= 'hello';

        return $item;
    }),
    F::WriteLine('output.txt'),
]);

$generator = $function('input.txt');
iterator_to_array($generator); // Iterate the generator to run.
```

---

### Convert
Convert from one structure to another.

##### TreeToLeaves
Takes a tree and returns all leaves.

##### RowsToTree
Takes an array of rows and turns into a tree.

##### TreeToRows
Takes a tree and turns into an array of rows.

---

### Dev

Functions helpful for development.

##### Dahbug
Prints every input using dahbug::dump().

##### DahbugWrite
Prints every input using dahbug::write().

##### Mute
Discards all input, preventing items from passing through.

---

### Dispatch
Functions for dispatching events for Observe-functions. For more information read [Observe](#observe).

##### DispatchError
Dispatches an error event if callback returns true.

##### DispatchEvent
Dispatches an event.

##### DispatchSideEffect
Dispatches a side effect.

---

### Flow
Functions wrapping other functions to control the "flow".

##### Compose
Sends the input to each inner function sequentially after each other, chaining them together. Output is the output of the last function in the chain.

```php
<?php
F::Compose([
    F::ReadLine(...),
    F::Map(...),
]);
```

##### Defer
Delays construction of the inner functions until execution. Input is sent to inner function, and output is the output of the inner function.

```php
<?php
F::Defer(function() {
    return F::Map(...);
});
```

##### Factory
Like Defer, but the inner function is reconstructed every time it's run.

##### Fork
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

##### Multiplex
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

---

### IO
Functions for handling files.

##### DirectoryFiles
Takes a directory path as input and outputs every file in the directory (with options for recursion).

##### ReadX
Takes a file as input and outputs its parsed contents.

##### WriteX
Writes input to a specified file.

##### MoveFile
Moves input file to a new path from a callback.

```php
<?php
F::MoveFile(function($file) {
    $date = date('Ymd');
    return str_replace('.csv', "-{$date}.csv", $file);
});
```

---

### Iterable
Functions for manipulating items in some way.

##### ApplyCombine
Applies a sub-function to each item, then merges them with the original item in a callback.

```php
<?php
F::ApplyCombine(
    F::Map(function($item) {
        return $item['value'] + 1;
    }),
    function($item, $results) {
        $item['plusone'] = $results;
        return $item;
    }
);
```

##### Expand
Yields item(s) from every input item.

```php
<?php
F::Expand(function($item) {
    foreach ($item['children'] as $child) {
        yield $child;
    }
});
```

##### Filter
Filters items based on a callback.

```php
<?php
F::Filter(function($item) {
    return $item['is_active'];
});
```

##### Group
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

##### Map
Edits every input item.

```php
<?php
F::Map(function($item) {
    $item['c'] = $item['a'] . $item['b'];

    return $item;
});
```

##### Merge
Sends every item through a sub-function and merges result with original item. The sub-function must return the same amount of items as input.

```php
<?php
F::Merge([
    F::Map(function($item) {
        return $item['id'];
    })
    F::Group(500),
    F::DispatchSideEffect('fetchNamesFromDatabase'),
    F::Expand(function($items) {
        foreach ($items as $item) {
            yield ['name' => $item];
        }
    }),
]);
```


##### Reduce
Reduce all input items into a single item.

```php
<?php
F::Reduce(function($carry, $item) {
    $carry += $item['qty'];

    return $carry;
}, 0);
```

---

### Observe
Functions for observing events dispatched by Dispatch-functions in the inner function. Events bubble outwards. Example:


##### AppendContext
Appends a context to events and then dispatches them again.

##### ObserveEvent
Observes events.

```php
F::ObserveEvent(
    F::DispatchEvent('hello'),
    [
        'hello' => function($item, $data, $contexts) {
            echo 'Hello event!';
        },
    ]
);

F::ObserveEvent(
    F::DispatchEvent('hello'),
    $class // Instance of class with a public hello() function
);
```

##### ObserveException
Observes thrown exceptions and then throws them again.

##### ObserveSideEffect
Observes side effects. Unlike events, side effect observers can edit the item being observed.

