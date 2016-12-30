<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend;

interface BackendInterface
{
    public function __construct($params);
    public function getData($inputData);
}
