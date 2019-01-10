Multiplex
=========

.. code-block:: php

    Multiplex ( callable $callback , array $functions )

Sends every input value to one of the inner :ref:`functions <functions>` based
on the result of the :ref:`callback <callback>` function.
Output is the output of the inner :ref:`functions <functions>`.


Parameters
----------

.. _callback:

:ref:`callback <callback>`
    .. code-block:: php

        scalar callback ( mixed $value )

    A callback that returns the key of the function to which the value should
    be passed.

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

.. literalinclude:: /../examples/functions/multiplex/1.php
    :language: php

Example #2
__________

By leaving a branch empty we can run functions for only some values.

.. literalinclude:: /../examples/functions/multiplex/2.php
    :language: php


See Also
--------

* :doc:`fork` - Send every input value to multiple functions.
