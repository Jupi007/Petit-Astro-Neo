<?php

declare(strict_types=1);

namespace App\Event\Definition;

use App\Event\Trait\CreatedActivityEventTrait;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use CreatedActivityEventTrait;
}
