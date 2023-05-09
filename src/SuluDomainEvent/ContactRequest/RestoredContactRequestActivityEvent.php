<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\ContactRequest;

use App\SuluDomainEvent\Trait\RestoredActivityEventTrait;

class RestoredContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RestoredActivityEventTrait;
}
