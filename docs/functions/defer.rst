Defer
=====

.. code-block:: php

    Defer ( callable $callback )

Delays construction of the inner function until execution. Input is sent to inner function, and
output is the output of the inner function. Defer is useful if for example constructing the inner
function is resource-intensive.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        mixed callback ( void )

    A callback that returns either a FunctionInterface or an array. If the callback returns an array
    it will be passed as argument to :doc:`compose`, which is then used as the inner function.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: /../examples/functions/defer/1.php
    :language: php
