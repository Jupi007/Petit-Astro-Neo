<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\ContactRequest;

use App\SuluDomainEvent\Trait\CreatedActivityEventTrait;

class CreatedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use CreatedActivityEventTrait;
}
