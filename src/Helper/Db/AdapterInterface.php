<?php

namespace Webbhuset\Bifrost\Helper\Db;

interface AdapterInterface
{
    public function quote($string);
    public function quoteIdentifier($string);
}
