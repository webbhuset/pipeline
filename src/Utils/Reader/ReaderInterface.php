<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader;

interface ReaderInterface
{
    public function __construct($params);
    public function init($args);
    public function getEntityCount();
    public function processNext();
    public function rewind();
    public function finalize();
}
