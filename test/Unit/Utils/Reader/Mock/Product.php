<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Reader\Mock;
use Webbhuset\Bifrost\Core\Utils\Reader\Mock as Core;

class Product extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

    public function getNextEntityTest()
    {

        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];

        $mockProduct = new Core\Product($params);
        $rowOne      = $mockProduct->getNextEntity();
        $rowTwo      = $mockProduct->getNextEntity();

        $this->newInstance($params)
            ->testThatArgs()->returns($rowOne)
            ->testThatArgs()->returns($rowTwo)
            ->testThatArgs()->returns(false);
    }
    public function initTest()
    {
    }

    public function getEntityCountTest()
    {
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $this->newInstance($params)
            ->testThatArgs()->returns(2);

        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 5,
        ];
        $this->newInstance($params)
            ->testThatArgs()->returns(5);
    }

    public function rewindTest()
    {
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];

        $mockProduct = new Core\Product($params);
        $rowOne      = $mockProduct->getNextEntity();
        $rowTwo      = $mockProduct->getNextEntity();
        $mockProduct->rewind();
        $rowOneAgain = $mockProduct->getNextEntity();
        $rowTwoAgain = $mockProduct->getNextEntity();

        if ($rowOne!==$rowOneAgain) {
            $this->addError('Result is not them same after rewind as the first time.');
        }
        if ($rowTwo!==$rowTwoAgain) {
            $this->addError('Result is not them same after rewind as the first time.');
        }

    }

    public function finalizeTest()
    {
    }
}
