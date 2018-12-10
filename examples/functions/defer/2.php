<?php
use Webbhuset\Pipeline\Constructor as F;

$function = F::Multiplex(
    function($value) {
        return $value >= 42 ? 'ok' : 'error';
    },
    [
        'ok'    => F::Observe(function($value) { /* Do nothing */ }),
        'error' => F::Defer(function() {
            $file = fopen('error.log');

            return F::Observe(function($value) use ($file) {
                $msg = "{$value} is less than 42.";

                fwrite($file, $msg);
            });
        }),
    ]
);
