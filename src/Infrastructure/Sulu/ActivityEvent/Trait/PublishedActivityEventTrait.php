<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Trait;

use App\Infrastructure\Sulu\ActivityEvent\ActivityEventType;

trait PublishedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Published;
    }
}
