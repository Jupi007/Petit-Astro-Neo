<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Definition;

use App\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use CreatedActivityEventTrait;
}
