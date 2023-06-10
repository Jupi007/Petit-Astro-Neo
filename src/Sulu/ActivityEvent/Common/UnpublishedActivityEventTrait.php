<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Common;

use App\Sulu\ActivityEvent\ActivityEventType;

trait UnpublishedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Unpublished;
    }
}
