<?php

use Webbhuset\Bifrost\Core as Bifrost;

class Webbhuset_Bifrost_Model_Job_Test
    extends Webbhuset_Bifrost_Model_Job_Abstract
{
    protected $_description = 'Imports the asdfs. By importing the asdfs the asdfs will be imported. The asdfs will not be imported if the qwertys have not been imported first.';

    protected $_aliases = [
        'a' => 'asdf',
    ];

    protected $_commands = [
        '-a, --asdf' => 'as the df\'s',
        '-b, --quite-a-long-command' => 'This command is pretty long',
    ];


    protected function _getComponent()
    {
        $pipeline = new Bifrost\Component\Flow\Pipeline(
            [
                new Bifrost\Component\Fetch\Directory(),
                new Bifrost\Component\Dev\Dahbug(),
            ]
        );

        return $pipeline;
    }

    protected function _getInput()
    {
        return '/var/www/m1/mary-kay/magento/var/whbifrost/import';
    }
}
