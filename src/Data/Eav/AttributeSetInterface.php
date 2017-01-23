<?php

namespace Webbhuset\Bifrost\Core\Data\Eav;

interface AttributeSetInterface
{
    public function getId();
    public function getName();
    public function getAttributes();
}
