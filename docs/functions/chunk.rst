Chunk
=====

.. code-block:: php

    Chunk(int $size)

Groups all input values into arrays of a specified size.
The last output array may contain less values.
This is similar to PHP's `array_chunk() <http://php.net/manual/en/function.array-chunk.php>`_.


Parameters
----------

size
    How many values should be in every chunk.


Examples
--------

Example #1
__________

Basic usage.

.. literalinclude:: ../../examples/functions/chunk/1.php
    :language: php

Example #2
__________

Using Chunk to batch database queries.

.. literalinclude:: ../../examples/functions/chunk/2.php
    :language: php


See Also
--------

* :doc:`group` - Group input values based on a callback function.
