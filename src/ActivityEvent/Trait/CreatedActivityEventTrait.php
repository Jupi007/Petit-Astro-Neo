<?php

declare(strict_types=1);

namespace App\ActivityEvent\Trait;

use App\ActivityEvent\ActivityEventType;

trait CreatedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Created;
    }
}
