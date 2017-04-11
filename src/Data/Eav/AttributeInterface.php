<?php

namespace Webbhuset\Bifrost\Data\Eav;

interface AttributeInterface
{
    public function getId();
    public function getCode();
    public function getBackendType();
    public function getScope();
    public function shouldUpdate();
}
