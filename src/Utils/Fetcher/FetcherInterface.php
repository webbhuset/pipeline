<?php
namespace Webbhuset\Bifrost\Core\Utils\Fetcher;

interface FetcherInterface
{
    public function __construct($params);
    public function init($args);
    public function fetch();
    public function getFilename();
}
