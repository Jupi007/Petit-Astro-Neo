<?php

declare(strict_types=1);

namespace App\Event\Trait;

use App\Event\ActivityEventType;

trait NotifiedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Notified;
    }
}
