Observe
=======

.. code-block:: php

    Observe ( callable $callback )

Passes every input value to the :ref:`callback <callback>` function without
modifying it.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        void callback ( mixed $value )

    .. _value:

    :ref:`value <value>`
        The current value.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: /../examples/functions/observe/1.php
    :language: php


See Also
--------

* :doc:`map` - Modify every input value with a callback function.
