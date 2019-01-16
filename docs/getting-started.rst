Getting Started
===============

Requirements
------------

* PHP 5.5 or greater.


Installation
------------

.. _Composer: https://getcomposer.org/

You can install Pipeline with `Composer`_ by adding ``"webbhuset/pipeline": "*"`` to your
composer.json or by running ``composer require webbhuset/pipeline`` in your terminal.


Building Pipeline Functions
---------------------------

The easiest way to construct Pipeline functions is to use the Constructor class. It has a static
functions for constructing :doc:`every Pipeline function <functions>`, allowing you to construct all
functions in a concise manner with a single use statement.

.. literalinclude:: /../examples/functions/take/1.php
    :language: php

It is of course also possible to construct the functions with ``new`` if that is preferred.


Using the Result
----------------

.. _Generator: http://php.net/manual/en/language.generators.php
.. _iterator_to_array(): http://php.net/manual/en/function.iterator-to-array.php

Since all Pipeline functions return a `Generator`_ they are actually not executed until the
generator is iterated, and you cannot iterate the result more than once.  If you need to iterate
more than once, consider converting the result to an array using `iterator_to_array()`_.
