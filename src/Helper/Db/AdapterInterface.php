<?php

namespace Webbhuset\Bifrost\Core\Helper\Db;

interface AdapterInterface
{
    public function quote($string);
    public function quoteIdentifier($string);
}
