<?php
use Webbhuset\Pipeline\Constructor as F;

$write = F::Compose([
    F::Reduce(function ($value, $carry) {
        if (!$carry) {
            $path = 'file.txt';
            $carry = [
                'path' => $path,
                'file' => fopen($path, 'w'),
            ];
        }

        fwrite($carry['file'], $value . "\n");

        return $carry;
    }),
    F::Filter(),
    F::Map(function ($carry) {
        fclose($carry['file']);

        return $carry['path'];
    }),
]);

iterator_to_array($write(range(1, 10)));
