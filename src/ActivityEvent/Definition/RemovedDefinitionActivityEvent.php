<?php

declare(strict_types=1);

namespace App\ActivityEvent\Definition;

use App\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RemovedActivityEventTrait;
}
