<?php

declare(strict_types=1);

namespace App\ActivityEvent\Definition;

use App\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RestoredActivityEventTrait;
}
