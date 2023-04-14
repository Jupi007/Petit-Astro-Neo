<?php

declare(strict_types=1);

namespace App\Event\ContactRequest;

use App\Event\Trait\CreatedActivityEventTrait;

class CreatedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use CreatedActivityEventTrait;
}
