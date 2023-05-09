<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\RestoredActivityEventTrait;

class RestoredPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RestoredActivityEventTrait;
}
