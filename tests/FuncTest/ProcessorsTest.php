<?php
namespace Webbhuset\Bifrost\Test\FuncTest;
use Webbhuset\Bifrost\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Test\FuncTest\ProcessorsTest\TestBridgeFactory;
require_once "../../../vendor/autoload.php";

class ProcessorsTest
{
    public function runTest()
    {
        $indata = [
            [
                'test1'  => [
                    "namn"       => 'Apa',
                    "beskrivning" => 'En grej',
                ],
                'test2'  => [
                    "artikelnummer" => '0531-001',
                    "pris"          => '156.45',
                    "status"        => '2',
                    "datum"         => null
                ],
            ]
        ];

        $bridgeFactory = new TestBridgeFactory(new NullLogger);
        $bridge        = $bridgeFactory->create();
        $writers       = $this->getWriters($bridge);

        $bridge->setData($indata);
        $bridge->processNext();

        if ($writers['varchar1']->getData()[0]['value'] !== 'Apa') {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['int1']->getData()[0]['value'] !== 2) {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['decimal1']->getData()[0]['value'] !== 156.45) {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['text1']->getData()[0]['value'] !== 'En grej') {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['datetime1']->getData()[0]['value'] !== '2017-01-02 00:00:00') {
            throw new \Exception('Unexpected Value');
        }

        $indata[0]['test1']['namn'] = 'ny apa';
        $bridge->setData($indata);
        $bridge->processNext();

        if ($writers['varchar2']->getData()[0]['value'] !== 'ny apa') {
            throw new \Exception('Unexpected Value');
        }

        $indata[0]['test1']['namn'] = 'ny apa igen';
        $bridge->setData($indata);
        $bridge->processNext();
        if ($writers['varchar2']->getData()[0]['value'] !== 'ny apa igen') {
            throw new \Exception('Unexpected Value');
        }

        $indata[0]['test2']['pris'] = '86.845';
        $bridge->setData($indata);
        $bridge->processNext();
        if ($writers['decimal2']->getData()[0]['value'] !== 86.845) {
            throw new \Exception('Unexpected Value');
        }

        $indata[0]['test2']['artikelnummer'] = '0531-00143';
        $bridge->setData($indata);
        $bridge->processNext();

        if ($writers['varchar1']->getData()[0]['value'] !== 'ny apa igen') {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['int1']->getData()[0]['value'] !== 2) {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['decimal1']->getData()[0]['value'] !== 86.845) {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['text1']->getData()[0]['value'] !== 'En grej') {
            throw new \Exception('Unexpected Value');
        }
        if ($writers['datetime1']->getData()[0]['value'] !== '2017-01-02 00:00:00') {
            throw new \Exception('Unexpected Value');
        }

        $indata[0]['test2']['datum'] = '2017-01-11 00:00:00';
        $bridge->setData($indata);
        $bridge->processNext();
        if ($writers['datetime2']->getData()[0]['value'] !== '2017-01-11 00:00:00') {
            throw new \Exception('Unexpected Value');
        }

        return true;
    }

    protected function getWriters($processor)
    {
        if (!$processor->getNextSteps()) {
            return [$processor->getId() => $processor];
        }

        $writers = [];
        foreach ($processor->getNextSteps() as $step) {
            $writers = array_merge($writers, $this->getWriters($step));
        }

        return $writers;
    }
}

$test = new ProcessorsTest();
$test->runTest();
