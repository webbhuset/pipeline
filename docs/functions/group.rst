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

        bool callback ( mixed $value , array $batch )

    If the callback function returns true the current :ref:`value <value>` is added to the current
    :ref:`batch <batch>`. If it returns false it is added to a new batch.

    .. _value:

    :ref:`value <value>`
        The current value.

    .. _batch:

    :ref:`batch <batch>`
        The current batch of grouped values.


Examples
--------

Example #1
__________

Basic usage example, grouping repeated values.

.. literalinclude:: /../examples/functions/group/1.php
    :language: php

Example #2
__________

Group values in groups where the sum of their values is >= 10, and uses :doc:`filter` to filter any
trailing group.

.. literalinclude:: /../examples/functions/group/2.php
    :language: php


See Also
--------

* :doc:`chunk` - Group input values into arrays of a specified size.
