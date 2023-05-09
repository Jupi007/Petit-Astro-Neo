<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\ContactRequest;

use App\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RestoredActivityEventTrait;
}
