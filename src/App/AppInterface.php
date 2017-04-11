<?php
namespace Webbhuset\Bifrost\App;

interface AppInterface
{
    public function run(Webbhuset\Bifrost\Job $job);
}
