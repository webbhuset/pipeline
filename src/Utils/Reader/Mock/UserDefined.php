<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader\Mock;
use \Webbhuset\Bifrost\Core\Utils\Reader\AbstractReader;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;

class UserDefined extends AbstractReader
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
