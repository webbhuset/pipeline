<?php
namespace Webbhuset\Bifrost\Core\App;

interface AppInterface
{
    public function run(Webbhuset\Bifrost\Job $job);
}
