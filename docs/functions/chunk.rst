Chunk
=====

.. code-block:: php

    Chunk ( int $size )

.. _array_chunk(): http://php.net/manual/en/function.array-chunk.php

Groups all input values into arrays with :ref:`size <size>` values in each array. The last output
array may contain less than :ref:`size <size>` values. This is similar to PHP's `array_chunk()`_.


Parameters
----------

.. _size:

:ref:`size <size>`
    How many values should be in every chunk.


Examples
--------

Example #1
__________

Basic usage.

.. literalinclude:: /../examples/functions/chunk/1.php
    :language: php

Example #2
__________

Using Chunk to batch database queries.

.. literalinclude:: /../examples/functions/chunk/2.php
    :language: php


See Also
--------

* :doc:`group-while` - Group input values based on a callback function.
