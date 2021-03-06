Reduce
======

.. code-block:: php

    Reduce ( callable $callback [, mixed $initialValue = [] ] )

Reduces all input values to a single value with the :ref:`callback <callback>` function.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        mixed callback ( mixed $value , mixed $carry )

    .. _value:

    :ref:`value <value>`
        The current value that is being reduced.

    .. _carry:

    :ref:`carry <carry>`
        The return value of previous iteration.

.. _initialValue:

:ref:`initialValue <initialValue>`
    The initial value of $carry.


Examples
--------

Example #1
__________

Basic usage example, summing all input values.

.. literalinclude:: /../examples/functions/reduce/1.php
    :language: php


Example #2
__________

Using Reduce, :doc:`filter`, and :doc:`map` to write to a file and return the path.  Reduce opens a
file pointer, and :doc:`map` closes it and returns the path.

Since Reduce always returns a value even with an empty input, we use :doc:`filter` to prevent an
attempt to close a non-existent pointer and returning the path when input is empty.

.. literalinclude:: /../examples/functions/reduce/2.php
    :language: php


See Also
--------

* :doc:`group-while` - Group input values based on a callback function.
* :doc:`scan` - Reduce input values, returning the intermediate results.
