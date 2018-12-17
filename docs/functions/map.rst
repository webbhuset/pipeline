Map
===

.. code-block:: php

    Map ( callable $callback )

Modify every input value with a :ref:`callback <callback>` function.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        mixed callback ( mixed $value )

    .. _value:

    :ref:`value <value>`
        The current value.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: ../../examples/functions/map/1.php
    :language: php


See Also
--------

* :doc:`expand` - Yield one or more values from every input value.
* :doc:`observe` - Send every input value to a callback function without modifying it.
