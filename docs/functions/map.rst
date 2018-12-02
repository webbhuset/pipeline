Map
===

.. code-block:: php

    Map(callable $callback)

Modify every input value with a callback.

Parameters
----------

callback
    .. code-block:: php

        mixed callback (mixed $value)

    value
        The value being modified

Examples
--------

Example #1 - Basic Usage
________________________

.. code-block:: php

    <?php
    use Webbhuset\Whaskell\Constructor as F;

    $map = F::Map(function($value) {
        return $value * 2;
    });

    $input = [1, 2, 5, 12];

    foreach ($map($input) as $value) {
        echo $value . "\n";
    }


Output::

    2
    4
    10
    24

See Also
--------

* :doc:`expand`
