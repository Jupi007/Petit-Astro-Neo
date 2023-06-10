<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Common;

use App\Sulu\ActivityEvent\ActivityEventType;

trait RestoredActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Restored;
    }
}
