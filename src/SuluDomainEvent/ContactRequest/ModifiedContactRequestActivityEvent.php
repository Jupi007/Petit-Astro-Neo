<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\ContactRequest;

use App\SuluDomainEvent\Trait\ModifiedActivityEventTrait;

class ModifiedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use ModifiedActivityEventTrait;
}
