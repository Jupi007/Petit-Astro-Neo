<?php

declare(strict_types=1);

namespace App\ActivityEvent\ContactRequest;

use App\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use CreatedActivityEventTrait;
}
