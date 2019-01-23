TakeWhile
=========

.. code-block:: php

    TakeWhile ( callable $callback )

Returns input values while the :ref:`callback <callback>` function returns true, then all remaining
values are ignored. The last value returned is the the one previous to the value for which the
:ref:`callback <callback>` function returned false.


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

.. literalinclude:: /../examples/functions/take-while/1.php
    :language: php


See Also
--------

* :doc:`take` - Take a specific amount of input values.
* :doc:`drop-while` - Discard input values while a callback function returns true.
