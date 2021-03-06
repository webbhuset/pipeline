List of Pipeline Functions
==========================

Pipeline functions are divided into two types: :ref:`iterable-functions` and :ref:`flow-functions`.
Value functions work with the values and modify them, while Flow functions are wrappers for other
functions allowing you to combine multiple functions into one.


.. _iterable-functions:

Value Functions
------------------

.. toctree::
    :hidden:
    :maxdepth: 1

    functions/chunk
    functions/drop
    functions/drop-while
    functions/expand
    functions/filter
    functions/group-while
    functions/map
    functions/observe
    functions/reduce
    functions/scan
    functions/take
    functions/take-while

* :doc:`/functions/chunk` - Group input values in groups of a specified size.
* :doc:`/functions/drop` - Discard the first N input values and return the rest.
* :doc:`/functions/drop-while` - Discard input values while callback returns true.
* :doc:`/functions/expand` - Yields one or more values from every input value.
* :doc:`/functions/filter` - Discard input values based on a callback.
* :doc:`/functions/group-while` - Group input values based on a callback.
* :doc:`/functions/map` - Modify every input value with a callback.
* :doc:`/functions/observe` - Send input values to a callback without modifying them.
* :doc:`/functions/reduce` - Reduce all input values to a single value.
* :doc:`/functions/scan` - Reduce all input values, returning the intermediate results.
* :doc:`/functions/take` - Return the first N input values and discard the rest.
* :doc:`/functions/take-while` - Return input values while callback returns true.


.. _flow-functions:

Flow Functions
--------------

.. toctree::
    :hidden:
    :maxdepth: 1

    functions/compose
    functions/defer
    functions/fork
    functions/multiplex

* :doc:`/functions/compose` - Chain functions together.
* :doc:`/functions/defer` - Delay construction of a function.
* :doc:`/functions/fork` - Send every input value to multiple functions.
* :doc:`/functions/multiplex` - Send every input value to one function based on a callback.
