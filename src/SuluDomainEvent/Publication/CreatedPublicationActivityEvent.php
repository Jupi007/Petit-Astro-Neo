<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\CreatedActivityEventTrait;

class CreatedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use CreatedActivityEventTrait;
}
