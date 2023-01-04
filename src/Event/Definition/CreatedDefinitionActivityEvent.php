<?php

declare(strict_types=1);

namespace App\Event\Definition;

use App\Event\ActivityEventType;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Created;
    }
}
