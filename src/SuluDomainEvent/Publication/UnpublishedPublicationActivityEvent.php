<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\UnpublishedActivityEventTrait;

class UnpublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use UnpublishedActivityEventTrait;
}
