<?php

declare(strict_types=1);

namespace App\Event\ContactRequest;

use App\Event\Trait\RestoredActivityEventTrait;

class RestoredContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RestoredActivityEventTrait;
}
