Scan
====

.. code-block:: php

    Scan(callable $callback, mixed $initialValue = [])

Reduces all input values to a single value with the callback function,
while returning the intermediate result of every iteration.

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

.. literalinclude:: ../../examples/functions/scan/1.php
    :language: php


Example #2
__________

Building a string, and using :doc:`drop` to skip the initial value.

.. literalinclude:: ../../examples/functions/scan/2.php
    :language: php


See Also
--------

* :doc:`reduce` - Reduce input values, returning only the final value.
