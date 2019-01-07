DropWhile
=========

.. code-block:: php

    DropWhile ( callable $callback )

Discards input values while the :ref:`callback <callback>` function returns true,
then all remaining values are returned. The first value returned is the one for
which the :ref:`callback <callback>` function returned false.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        bool callback ( mixed $value )

    .. _value:

    :ref:`value <value>`
        The current value.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: /../examples/functions/drop-while/1.php
    :language: php


See Also
--------

* :doc:`drop` - Discard a specific amount of input values.
* :doc:`filter` - Discard input values based on a callback.
* :doc:`take-while` - Return input values while callback returns true.
