<?php

namespace Webbhuset\Bifrost\Core\Data;

class Error
{
    protected $data;
    protected $errors;

    public function __construct($errors, $data)
    {
        $this->data     = $data;
        $this->errors   = $errors;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
