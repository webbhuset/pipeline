<?php

namespace Webbhuset\Whaskell\Observe;

interface ObserverInterface
{
    public function observeEvent($name, $item, $data, $contexts = []);
    public function observeSideEffect($name, $item, $data);
    public function observeError($item, $data, $contexts = []);
}
