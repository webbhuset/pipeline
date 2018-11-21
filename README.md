# Whaskell

Whaskell is a collection of functions for manipulating data designed so that they can be chained together.

## Functions

Every Whaskell function is a class implementing \_\_invoke(), thus allowing instances to be run as functions. Every function takes a Traversable as input and returns a Generator. Functions are constructed using the static methods in the Constructor class.  
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
