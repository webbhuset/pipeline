Scan
====

.. code-block:: php

    Scan ( callable $callback [, mixed $initialValue = [] ] )

Reduces all input values to a single value with the :ref:`callback <callback>`
function, while returning the intermediate result of every iteration.

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

.. literalinclude:: /../examples/functions/scan/1.php
    :language: php


Example #2
__________

Building a string, and using :doc:`drop` to skip the initial value.

.. literalinclude:: /../examples/functions/scan/2.php
    :language: php


See Also
--------

* :doc:`reduce` - Reduce input values, returning only the final value.
