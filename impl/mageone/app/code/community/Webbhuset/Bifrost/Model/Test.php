<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Component;

class Webbhuset_Bifrost_Model_Test
{
    public function import($type)
    {
        $pipe = new Component\Flow\Pipeline([
            new Component\Transform\Expand(function($item) { // Source, instead of dir fetcher.
                yield 20; // file 1 from dir
                yield 30; // file 2 from dir
            }),
            $this->getTaskList($type),
            $this->getLogMonad(),
        ]);

        foreach ($pipe->process([1]) as $item) {
            //dahbug::dump($item);
        }
    }

    public function getTaskList($type)
    {
        switch ($type) {
            case 'product':
                return new Component\Flow\TaskList([
                    'options'  => new Component\Flow\Factory([$this, 'importOptions']),
                    'products' => new Component\Flow\Factory([$this, 'importProducts']),
                ], 'import');

            case 'customer':
                return new Component\Flow\TaskList([
                    'customer' => new Component\Flow\Factory([$this, 'importCustomers']),
                ], 'import');
        }
    }

    public function makeTestFile()
    {
        $now        = str_replace([' ', ':'], ['T', '-'], Varien_Date::now());
        $target     = Mage::getBaseDir('var') . "/whbifrost/import/product/products-{$now}.csv";
        $gen        = (new Core\Component\Read\Mock\Product($target))->process(20);

        $file       = new Core\Component\Write\File\Csv($target);

        foreach ($file->process($gen) as $b) {
        }
    }

    public function getProductReader()
    {
        return new Core\Component\Read\Mock\Product('seed');
    }

    public function importProducts()
    {
        $pipeline = [$this->getProductReader()];
        $pipeline = array_merge($pipeline, Mage::getModel('whbifrost/import_product_simple')->create());

        return new Component\Flow\Pipeline($pipeline);
    }

    public function importOptions()
    {
        $type       = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
        $factory    = Mage::getModel('whbifrost/service_import_eav_attribute_option');

        return new Component\Flow\Pipeline(array_merge(
            [$this->getProductReader()],
            $factory->createEntityFileReducer($type),
            $factory->createSequence($type)
        ));
    }

    public function importCustomers()
    {
        return new Component\Flow\Pipeline(array_merge(
            [new Component\Read\Mock\Customer('seed')],
            Mage::getModel('whbifrost/import_customer_entity')->create([])
        ));
    }

    public function getLogMonad()
    {
        return new Core\Component\Monad\Observer([
            'task_start' => [
                function ($item, $name, $data) {
                    $job  = $data['job'];
                    $task = $data['task'];
                    dahbug::toggleTimer("$job/$task");
                    \dahbug::dump("$job/$task - file: $item", 'TASK START');
                },
            ],
            'task_done' => [
                function ($item, $eventName, $data) {
                    $job  = $data['job'];
                    $task = $data['task'];
                    \dahbug::dump("$job/$task - file: $item", 'TASK DONE');
                    dahbug::toggleTimer("$job/$task");
                },
            ],
            'error' => [
                function ($error) {
                    \dahbug::dump($error->getErrors(), 'ERROR');
                },
            ],
            'created' => [
                function ($data) {
                    \dahbug::dump($data['entity_id'], 'CREATED');
                },
            ],
            'updated' => [
                function ($data) {
                    //\dahbug::dump(count($data), 'UPDATED');
                },
            ],
        ]);
    }
}
