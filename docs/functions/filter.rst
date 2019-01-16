Filter
======

.. code-block:: php

    Filter ( [ callable $callback ] )

Passes every input value to the :ref:`callback <callback>` function, returning only values for which
the :ref:`callback <callback>` returns true.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        bool callback ( mixed $value )

    .. _converted to bool: http://php.net/manual/en/language.types.boolean.php#language.types.boolean.casting

    If no :ref:`callback <callback>` is supplied, all values that equal false (after being
    `converted to bool`_) are removed.

    .. _value:

    :ref:`value <value>`
        The current value.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: /../examples/functions/filter/1.php
    :language: php


See Also
--------

* :doc:`drop` - Discard a specific amount of input values.
* :doc:`drop-while` - Discard input values while a callback function returns true.
