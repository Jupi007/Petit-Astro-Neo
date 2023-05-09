<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Trait;

use App\SuluDomainEvent\ActivityEventType;

trait RestoredActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::Restored;
    }
}
