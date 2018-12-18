Getting Started
===============

Installation
------------

You can install Pipeline with `Composer <https://getcomposer.org/>`_ by adding
``"webbhuset/pipeline": "*"`` to your composer.json or running
``composer require webbhuset/pipeline`` in your terminal.


Building Pipeline Functions
---------------------------

The easiest way to construct Pipeline functions is to use the Constructor class.
It has static functions for every Pipeline function, allowing you to construct
all functions in a concise manner with a single use statement.

.. code-block:: php

    <?php
    use Webbhuset\Pipeline\Constructor as F;

    $take5 = F::Take(5);

    $result = $take5(range(1,10));

It is of course also possible to construct the functions with ``new`` if that
is preferred.


Using the Result
----------------

.. _Generator: http://php.net/manual/en/language.generators.php
.. _iterator_to_array: http://php.net/manual/en/function.iterator-to-array.php

Since all Pipeline functions return a `Generator`_ they are actually not executed
until the generator is iterated, and you cannot iterate the result more than once.
If you need to iterate more than once you need to convert the result to an array
using `iterator_to_array`_.
