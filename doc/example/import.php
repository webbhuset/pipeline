<?php
require_once './vendor/autoload.php';

use Webbhuset\Bifrost\Core\Processor;

$chain = new Processor\Chain([
    new Processor\Map(function($item) {
        return [
            'name'  => $item['title'],
            'sku'   => $item['artno'],
            'price' => $item['cost'],
            'status' => 1,
        ];
    }),
    new Processor\Filter(function($item) {
        if ($item['sku'] == '100-5') {
            return false;
        }

        return true;
    }),
    new Processor\Fork([
        new Processor\Chain([
            new Processor\Map(function($item) {
                $item['fork'] = 1;
                return $item;
            }),
        ]),
        new Processor\Chain([
            new Processor\Reduce(function($carry, $item, $atEnd) {
                if ($atEnd) {
                    yield $carry;
                    return;
                }
                $carry[] = $item['sku'];

                if (count($carry) >= 4) {
                    yield $carry;
                    usleep(500000);
                }
            }, []),
        ]),
    ]),
    /*
    new Processor\Dahbug(),
    new Processor\Mute(),
    new Processor\Merge([
        new Processor\Filter(function($item) {
            if ($item['sku'] == '100-5') {
                return false;
            }

            return true;
        }),
        new Processor\Reduce(function($carry, $item) {
            $carry[] = $item['sku'];

            if (count($carry) >= 4) {
                yield $carry;
            }
        }, []),
        new Processor\Map(function($results) {
            $newItems = [];
            usleep(500000);
            foreach ($results as $item) {
                yield $item => ['id' => (int)preg_replace('/\d+-(\d+)/', '$1', $item)];
            }
        }),
    ]),
    */
]);


function products($count) {
    for ($i = 0; $i < $count; $i++) {
        dahbug::write("Read $i");
        usleep(100000);
        $product = [
            'title'     => "Product {$i}",
            'artno'     => "100-{$i}",
            'cost'      => "1{$i}.99",
        ];

        yield $product;
    }
}
$products = products(10);
$products = $chain->process($products);

foreach ($products as $product) {
    //usleep(300000);
    dahbug::dump($product, "Output");
    //dahbug::write('Write');
    //usleep(200000);
}

foreach ($chain->finalize() as $product) {
    //usleep(300000);
    dahbug::dump($product, "Finalize");
    //dahbug::write('Write');
    //usleep(200000);
}

