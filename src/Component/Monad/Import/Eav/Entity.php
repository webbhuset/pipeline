<?php

namespace Webbhuset\Bifrost\Core\Component\Monad\Import\Eav;

use Webbhuset\Bifrost\Core\Component\Monad;
use Webbhuset\Bifrost\Core\Component\Sequence\Import\Eav\Entity\ActionsInterface;

class Entity extends Monad\Standard
{
    protected $actions;

    public function __construct(ActionsInterface $actions)
    {
        parent::__construct($actions);
    }
}
