<?php

namespace Webbhuset\Bifrost\Data\Eav;

interface AttributeSetInterface
{
    public function getId();
    public function getName();
    public function getAttributes();
}
