<?php

namespace Webbhuset\Whaskell\Dispatch\Data;

class ErrorData extends EventData
{
    public function __construct($item, $message, array $contexts = []) {
        parent::__construct('error', $item, ['message' => $message], $contexts);
    }

    public function appendContext($context)
    {
        $contexts   = $this->contexts;
        $contexts[] = $context;

        return new static($this->item, $this->getMessage(), $contexts);
    }

    public function getMessage()
    {
        return $this->data['message'];
    }
}
