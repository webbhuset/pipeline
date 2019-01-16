Compose
=======

.. code-block:: php

    Compose ( array $functions )

Chains :ref:`functions <functions>` together, using the output of each function as input to the
next. Output is the output of the last function in the chain.


Parameters
----------

.. _functions:

:ref:`functions <functions>`
    Array of functions that should be chained. If the array is multidimensional and/or contains
    another Compose it will be flattened.


Examples
--------

Example #1
__________

Basic usage.

.. literalinclude:: /../examples/functions/compose/1.php
    :language: php

Example #2
__________

Demonstrating how multidimensional arrays and other Composes are flattened.

.. literalinclude:: /../examples/functions/compose/2.php
    :language: php


See Also
--------

* :doc:`fork` - Send every input value to multiple functions.
