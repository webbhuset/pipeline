<?php

require_once "../src/Autoload.php";
Webbhuset\Bifrost\Core\Autoload::registerBase('Webbhuset\Bifrost\Test', dirname(__file__));


$shouldBeTested  = array();

$nameSpaces = [
    '../src' => 'Webbhuset\\Bifrost\\Core'
];

foreach ($nameSpaces as $dir => $namespace) {
    $shouldBeTested[$namespace] = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $filename) {
        if (strtolower($filename->getExtension()) == 'php') {
            $className = preg_replace('#.*src/([\w/]+).php#', '\1', (string)$filename);
            $className = str_replace('/', '\\', $className);
            if (preg_match('/Autoload|Abstract|Interface$/', $className)) {
                continue;
            }
            $shouldBeTested[$namespace][] = $className;
        }
    }
}

$args = [
];

foreach ($shouldBeTested as $namespace => $classes) {
    foreach ($classes as $class) {
        $subject = $namespace . '\\' . $class;
        $unitTestClass = 'Webbhuset\\Bifrost\\Test\\Unit\\' . $class;
        if (!class_exists($subject)) {
            echo "Class is missing: {$subject}\n";
            continue;
        }

        if (!class_exists($unitTestClass)) {
            echo "Class unit test missing: {$subject}\n";
            continue;
        }

        $unitTest = new $unitTestClass($subject);
        $unitTest->run($args);
    }
}
