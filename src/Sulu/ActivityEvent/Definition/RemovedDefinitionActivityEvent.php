<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Definition;

use App\Sulu\ActivityEvent\Common\RemovedActivityEventTrait;

class RemovedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RemovedActivityEventTrait;
}
