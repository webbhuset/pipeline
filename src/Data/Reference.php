<?php

namespace Webbhuset\Bifrost\Core\Data;

class Reference
{
    public $data;

    public function __construct()
    {
        $data = func_get_args();
        $this->data = $data;
    }
}
