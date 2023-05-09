<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\ContactRequest;

use App\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use CreatedActivityEventTrait;
}
