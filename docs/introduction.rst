Introduction
============

What is Pipeline?
-----------------

Pipeline is a PHP library for building reusable functions for manipulating values. Taking ideas
from functional programming languages, the library promotes you to build small functions that each
do one specific thing, and then combining them together to achieve what you want.


When Should I Use Pipeline?
---------------------------

.. _array_map(): http://php.net/manual/en/function.array-map.php

Most of Pipeline's functions' functionality is available in built-in PHP functions. For example, if
the only thing you want to do is to map an array, you'll be better off using PHP's `array_map()`_
instead of :doc:`functions/map`. Pipeline's usefulness comes into play when you want to combine
functions, e.g. build a function that reads input files, maps their data, and imports the data to a
database.

Of course you could just write a normal PHP function, but Pipeline handles all function chaining for
you, and makes it easier to understand the flow of data at a glance. Additionally every Pipeline
function is lazy, meaning you (usually) don't have to worry about large amount of data. Compare the
following functions:

.. code-block:: php

    <?php

    // Using normal PHP
    function importToDatabase(array $files, int $batchSize = 100) {
        foreach ($files as $file) {
            $rows = $this->readRowsFromFile($file);
            foreach ($rows as $idx => $row) {
                $rows[$idx] = $this->mapFileData($row);
            }

            $chunks = array_chunk($rows, $batchSize);
            foreach ($chunks as $chunk) {
                $this->importRowsToDatabase($chunk);
            }

            foreach ($rows as $row) {
                $logData = $this->formatLogData($data);
                $this->logDataToFile($logData);
            }
        }
    }

    // Using Pipeline
    function importToDatabase(array $files, int $batchSize = 100)
    {
        $fun = F::Compose([
            F::Expand([$this, 'readRowsFromFile']),
            F::Map([$this, 'mapFileData']),
            F::Fork([
                [
                    F::Chunk($batchSize),
                    F::Observe([$this, 'importRowsToDatabase']),
                ],
                [
                    F::Map([$this, 'formatLogData']),
                    F::Observe([$this, 'logDataToFile']),
                ]
            ])
        ]);

        iterator_to_array($fun($files));
    }
