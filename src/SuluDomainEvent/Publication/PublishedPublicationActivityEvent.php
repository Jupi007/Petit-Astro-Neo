<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\PublishedActivityEventTrait;

class PublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use PublishedActivityEventTrait;
}
