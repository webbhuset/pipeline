<?php

namespace Webbhuset\Bifrost\Data\Eav;

interface AttributeInterface
{
    public function getId();
    public function getCode();
    public function getTable();
    public function getType();
    public function getScope();
}
