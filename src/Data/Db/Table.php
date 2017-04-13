<?php

namespace Webbhuset\Bifrost\Data\Db;

class Table
{
    protected $data;


    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getColumns()
    {
        return $this->data['columns'];
    }

    public function getDimensions()
    {
        return $this->data['dimensions'];
    }

    public function getStaticColumns()
    {
        return $this->data['staticColumns'];
    }

    public function getMapper()
    {
        return $this->data['mapper'];
    }
}
