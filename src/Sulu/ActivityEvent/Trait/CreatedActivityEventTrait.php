<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Trait;

use App\Sulu\ActivityEvent\ActivityEventType;

trait CreatedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Created;
    }
}
