Fork
====

.. code-block:: php

    Fork ( array $functions )

Sends every input value to every inner function. Output is the output of every inner function.


Parameters
----------

.. _functions:

:ref:`functions <functions>`
    An array of Pipeline functions. If any of the elements in the array is
    an array it will be passed as argument to :doc:`compose`, which is then
    used as the function.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: ../../examples/functions/fork/1.php
    :language: php


See Also
--------

* :doc:`compose` - Chain functions together sequentially.
* :doc:`multiplex` - Send every input value to one function based on a callback function.
