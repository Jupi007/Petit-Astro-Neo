<?php

declare(strict_types=1);

namespace App\Event\Trait;

use App\Event\ActivityEventType;

trait PublishedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Published;
    }
}
