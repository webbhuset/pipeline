<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;

interface WriterInterface
{
    public function __construct($params);
    public function init($args);
    public function putEntity($data);
    public function finalize();
}
