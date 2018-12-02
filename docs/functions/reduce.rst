Reduce
======

.. code-block:: php

    Reduce(callable $callback, mixed $initialValue = [])

Reduce all input values into a single value.

Parameters
----------

callback
    .. code-block:: php

        mixed callback (mixed $value, mixed $carry)

    value
        Current value.

    carry
        Return value of previous iteration.



initialValue
    The initial value of $carry.


Examples
--------

Example 1
_________

.. code-block:: php

    <?php
    F::Reduce(function($value, $carry) {
        $carry += $value['qty'];

        return $carry;
    }, 0);


See Also
--------

* :doc:`group`
