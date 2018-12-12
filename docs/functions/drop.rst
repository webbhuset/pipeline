Drop
====

.. code-block:: php

    Drop ( int $amount )

Discards the first :ref:`amount <amount>` input values, returning the remaining values.


Parameters
----------

.. _amount:

:ref:`amount <amount>`
    The amount of input values that should be discarded.


Examples
--------

Example #1
__________

Basic usage.

.. literalinclude:: ../../examples/functions/drop/1.php
    :language: php


See Also
--------

* :doc:`drop-while` - Discard input values based on a callback function.
* :doc:`take` - Return a specific amount of input values.
