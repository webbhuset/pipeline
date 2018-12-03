List of Functions
=================

Iterable Functions
------------------

.. toctree::
    :hidden:
    :maxdepth: 1

    functions/chunk
    functions/drop
    functions/drop-while
    functions/expand
    functions/filter
    functions/group
    functions/map
    functions/observe
    functions/reduce
    functions/scan
    functions/take
    functions/take-every
    functions/take-while

* :doc:`/functions/chunk` - Group input values in groups of a specified size.
* :doc:`/functions/drop` - Discard the first N input values and return the rest.
* :doc:`/functions/drop-while` - Discard input values while callback returns true.
* :doc:`/functions/expand` - Yields one or more values from every input value.
* :doc:`/functions/filter` - Remove input values based on a callback.
* :doc:`/functions/group` - Group input values based on a callback.
* :doc:`/functions/map` - Modify every input value with a callback.
* :doc:`/functions/observe` - Send input values to a callback without modifying them.
* :doc:`/functions/reduce` - Reduce all input values to a single value.
* :doc:`/functions/scan` - Reduce all input values, returning the intermediate results.
* :doc:`/functions/take` - Return the first N input values and discard the rest.
* :doc:`/functions/take-every` - Return every N\ :sup:`th` input value.
* :doc:`/functions/take-while` - Return input values while callback returns true.

Flow Functions
--------------

.. toctree::
    :hidden:

    functions/compose
    functions/defer
    functions/factory
    functions/fork
    functions/multiplex

* :doc:`/functions/compose` - Chain functions together.
* :doc:`/functions/defer` - Delay construction of a function.
* :doc:`/functions/factory` - Construct a function for every input value.
* :doc:`/functions/fork` - Send every input value to multiple functions.
* :doc:`/functions/multiplex` - Send every input value to one function based on a callback.
