Expand
======

.. code-block:: php

    Expand([callable $callback])


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        Generator callback (mixed $value)

    Callback function that returns a Generator.

    If no :ref:`callback <callback>` is supplied, Expand will ``yield from`` every value.

    .. _value:

    :ref:`value <value>`
        The current value.

Examples
--------

Example #1
__________


See Also
--------
