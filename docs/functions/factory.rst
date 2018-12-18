Factory
=======

.. code-block:: php

    Factory ( callable $callback )

Constructs an inner function using :ref:`callback <callback>` for every input
value and sends the input value to the constructed function.
Output is the output of the inner function.
Factory is useful if the inner construction is dependent on some outer state that
might change between input values, or dependent on the value itself.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        mixed callback ( mixed $value )

    A callback that returns either a FunctionInterface or an array.
    If the callback returns an array it will be passed as argument to :doc:`compose`,
    and then that Compose will be used as the inner function.

    .. _value:

    :ref:`value <value>`
        The current value.


Examples
--------

Example #1
__________

Basic usage example.

.. literalinclude:: /../examples/functions/factory/1.php
    :language: php


See Also
--------

* :doc:`defer` - Delay construction of a function.
* :doc:`multiplex` - Send every input value to one function based on a callback.
