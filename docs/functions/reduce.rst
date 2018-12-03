Reduce
======

.. code-block:: php

    Reduce(callable $callback, mixed $initialValue = [])

Reduces all input values to a single value with the callback function.

Parameters
----------

callback
    .. code-block:: php

        mixed callback (mixed $value, mixed $carry)

    value
        The current value that is being reduced.

    carry
        The return value of previous iteration.

initialValue
    The initial value of $carry.


Examples
--------

Example #1
__________

Basic example, summing all input values.

.. literalinclude:: ../../examples/functions/reduce/1.php
    :language: php


See Also
--------

* :doc:`group` - Group input values based on a callback function.
* :doc:`scan` - Reduce input values, returning the intermediate results.
