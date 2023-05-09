<?php

declare(strict_types=1);

namespace App\ActivityEvent\Definition;

use App\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use CreatedActivityEventTrait;
}
