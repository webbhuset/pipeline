Group
=====

.. code-block:: php

    Group ( callable $callback )

Groups input values into arrays based on the :ref:`callback <callback>` function.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        bool callback ( mixed $value, array $batch, bool $finalize )

    If the callback function returns true the current :ref:`value <value>` is
    added to the current :ref:`batch <batch>`. If it returns false it is added
    to a new batch.

    .. _value:

    :ref:`value <value>`
        The current value.

    .. _batch:

    :ref:`batch <batch>`
        The current batch of grouped values.

    .. _finalize:

    :ref:`finalize <finalize>`
        After iteration is finished the :ref:`callback <callback>` function
        will be called one final time with this parameter set as true and a
        :ref:`value <value>` of null. If the :ref:`callback <callback>`
        function returns true for this final call the current
        :ref:`batch <batch>` is returned, else it is discarded.


Examples
--------

Example #1
__________

Basic usage example, grouping sequentially repeated values.

.. literalinclude:: ../../examples/functions/group/1.php
    :language: php

Example #2
__________

Group values in groups where the sum of their values is >= 10, and discards the
last group if its sum of its values is < 10.

.. literalinclude:: ../../examples/functions/group/2.php
    :language: php


See Also
--------

* :doc:`chunk` - Group input values into arrays of a specified size.
