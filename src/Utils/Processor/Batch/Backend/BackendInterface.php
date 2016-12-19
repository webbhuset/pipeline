<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor\Batch\Backend;

interface BackendInterface
{
    public function __construct($params);
    public function init($args);
    public function processData($data);
    public function finalize();
}
