<?php

declare(strict_types=1);

namespace App\ActivityEvent\ContactRequest;

use App\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RestoredActivityEventTrait;
}
