<?php

declare(strict_types=1);

namespace App\Event\Definition;

use App\Event\Trait\RemovedActivityEventTrait;

class RemovedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RemovedActivityEventTrait;
}
