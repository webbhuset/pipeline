<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader\Mock;
use \Webbhuset\Bifrost\Core\Utils\Reader\ReaderInterface;

class Product implements ReaderInterface
{
    protected $noOfEntities;
    protected $dataGenerator;
    protected $counter = 0;

    public function __construct($params)
    {
        $this->noOfEntities = $params['no_of_entities'];
        $this->dataGenerator = new \Webbhuset\Bifrost\Core\Utils\DataGenerator;
        $this->dataGenerator->setGlobalSeed($params['seed']);
    }

    public function init($args)
    {

    }

    public function rewind()
    {
        $this->counter = 0;
    }

    public function getEntityCount()
    {
        return $this->noOfEntities;
    }

    public function getNextEntity()
    {
        if ($this->counter >= $this->noOfEntities) {
            return false;
        }

        $generator = $this->dataGenerator->setRowSeed($this->counter);
        $data      = [
            'name'                  => $generator->getString(5, 12, 'name'),
            'description'           => $generator->getString(50, 120, 'description'),
            'short_description'     => $generator->getString(20, 50, 'short_description'),
            'sku'                   => $generator->getString(3, 10, 'sku'),
            'price'                 => $generator->getFloat(10, 50000, 'price'),
            'special_price'         => $generator->getFloat(10, 50000, 'special_price'),
            'special_from_date'     => $generator->getDate('2015-12-01', '2017-02-01', 'special_from_date'),
            'special_to_date'       => $generator->getDate('2015-12-01', '2017-02-01', 'special_to_date'),
            'cost'                  => $generator->getFloat(10, 50000, 'cost'),
            'weight'                => $generator->getFloat(10, 50000, 'weight'),
            'manufacturer'          => $generator->getString(5, 12, 'manufacturer'),
            'meta_title'            => $generator->getString(5, 12, 'meta_title'),
            'meta_keyword'          => $generator->getString(5, 12, 'meta_keyword'),
            'meta_description'      => $generator->getString(50, 120, 'meta_description'),
            'image'                 => $generator->getString(5, 12, 'image'),
            'small_image'           => $generator->getString(5, 12, 'small_image'),
            'thumbnail'             => $generator->getString(5, 12, 'thumbnail'),
            'color'                 => $generator->getString(5, 12, 'color'),
            'qty'                   => $generator->getInt(0, 150, 'qty'),
            'is_in_stock'           => $generator->getBool('is_in_stock'),
        ];
        $this->counter++;

        return $data;
    }

    public function finalize()
    {

    }
}
