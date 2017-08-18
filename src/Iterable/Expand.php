<?php

namespace Webbhuset\Whaskell\Iterable;

$phpVersion = phpversion();

switch (1) {
    case version_compare($phpVersion, '7.0'): return require_once 'Expand-7.0.php';
    case version_compare($phpVersion, '5.5'): return require_once 'Expand-5.5.php';
}
