Multiplex
=========

.. code-block:: php

    Multiplex(callable $callback, array $functions)

Sends the input to one of the inner function based on the result of the callback.
Output is the output of the inner function.


Parameters
----------

callback
    Callback

functions
    Functions.


Examples
--------

Example #1
__________

.. code-block:: php

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


See Also
--------
