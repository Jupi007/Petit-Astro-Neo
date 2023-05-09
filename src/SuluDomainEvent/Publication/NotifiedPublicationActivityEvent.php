<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\NotifiedActivityEventTrait;

class NotifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use NotifiedActivityEventTrait;
}
