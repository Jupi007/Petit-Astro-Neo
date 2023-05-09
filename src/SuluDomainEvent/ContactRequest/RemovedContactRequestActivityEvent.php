<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\ContactRequest;

use App\SuluDomainEvent\Trait\RemovedActivityEventTrait;

class RemovedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RemovedActivityEventTrait;
}
