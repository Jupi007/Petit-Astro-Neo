<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Definition;

use App\Infrastructure\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use CreatedActivityEventTrait;
}
