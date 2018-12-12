Expand
======

.. code-block:: php

    Expand ( [ callable $callback ] )

Applies :ref:`callback <callback>` to every input value, yielding from the resulting Generator.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        Generator callback (mixed $value)

    Callback function that returns a Generator.

    If no :ref:`callback <callback>` is supplied, Expand will ``yield from`` every input value.

    .. _value:

    :ref:`value <value>`
        The current value.

Examples
--------

Example #1
__________

Basic usage with default :ref:`callback <callback>`.

.. literalinclude:: ../../examples/functions/expand/1.php
    :language: php

Example #2
__________

Using Expand to create values of the cartesian product of two arrays.

.. literalinclude:: ../../examples/functions/expand/2.php
    :language: php


See Also
--------

* :doc:`map` - Modify every input value with a callback function.
