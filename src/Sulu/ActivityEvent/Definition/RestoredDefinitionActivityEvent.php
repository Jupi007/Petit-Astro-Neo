<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Definition;

use App\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RestoredActivityEventTrait;
}
