<?php

namespace Webbhuset\Whaskell;

$phpVersion = phpversion();

switch (1) {
    case version_compare($phpVersion, '5.6'): return require_once 'Constructor-5.6.php';
    case version_compare($phpVersion, '5.5'): return require_once 'Constructor-5.5.php';
}
