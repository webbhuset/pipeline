Take
====

.. code-block:: php

    Take ( int $amount )

Returs the first :ref:`amount <amount>` input values, discarding the remaining values.


Parameters
----------

.. _amount:

:ref:`amount <amount>`
    The amount of input values to take.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: /../examples/functions/take/1.php
    :language: php


See Also
--------

* :doc:`drop` - Discard a specific amount of input values.
* :doc:`take-while` - Return input values while a callback function returns true.
