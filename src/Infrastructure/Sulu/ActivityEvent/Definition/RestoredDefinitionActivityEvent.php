<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Definition;

use App\Infrastructure\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RestoredActivityEventTrait;
}
