Useful Information
==================

Pipeline and States
-------------------

.. __: http://php.net/manual/en/language.oop5.magic.php#object.invoke

While some of Pipeline's functions are stateless (e.g. :doc:`functions/map` and
:doc:`functions/expand`), others (e.g. :doc:`functions/take` and :doc:`functions/reduce`) keep a
state in some situations. Specifically, if $keepState (the second argument to `__invoke()`__ for
every function) is true then these function will keep a state even after their generator has been
fully iterated. As an example, :doc:`functions/take` will remember how many values it has returned:

.. literalinclude:: /../examples/state-take.php
    :language: php

Since $keepState defaults to false this is normally not something that you have to worry about when
using Pipeline, and is mostly used internally by some of the :ref:`flow-functions`.

States can also cause issues if multiple generators are created from these functions and iterated
simultaneously (even if $keepState is false). This is most easily circumvented by using a
:doc:`functions/defer` to build the functions separately for every input:

.. literalinclude:: /../examples/state-defer.php
    :language: php


Separating Flow and Logic
-------------------------

Just like when writing normal functions it is preferable to have multiple functions with descriptive
names instead of adding everything into one function that does everything. Additionally this
promotes separating functions responsible for the flow of data and functions responsible for
manipulating data.  Compare the following:

Using named functions:

.. code-block:: php

    <?php
    class myFunctionBuilder
    {
        public function buildMyFunction()
        {
            return [
                $this->mapRows(),
                $this->filterInvalid(),
                $this->insertToDatabase(),
            ];
        }

        protected function mapRows()
        {
            return F::Map(function ($value) {
                // ...
            });
        }

        protected function filterInvalid()
        {
            return F::Filter(function ($value) {
                // ...
            });
        }

        protected function insertToDatabase()
        {
            return F::Observe(function ($value) {
                // ...
            });
        }
    }


Without named functions:

.. code-block:: php

    <?php
    class myFunctionBuilder
    {
        public function buildMyFunction()
        {
            return [
                F::Map(function ($value) {
                    // ...
                }),
                F::Filter(function ($value) {
                    // ...
                }),
                F::Observe(function ($value) {
                    // ...
                }),
            ];
        }
    }
