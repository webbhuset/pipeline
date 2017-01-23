<?php

require 'abstract.php';
if (!class_exists('dahbug')) {
    require_once '/var/www/tools/dev-docker/tools/dahbug/dahbug.php';
}


class Webbhuset_Bifrost_Shell
    extends Mage_Shell_Abstract
{
    public function run()
    {
        $import = Mage::getModel('whbifrost/test');

        if ($this->getArg('make')) {
            $import->makeTestFile();

            return;
        }
        if ($this->getArg('import')) {
            $import->import($this->getArg('import'));

            return;
        }
        $import->test();
    }
}

$shell = new Webbhuset_Bifrost_Shell;
$shell->run();
