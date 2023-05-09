<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\DraftRemovedActivityEventTrait;

class DraftRemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use DraftRemovedActivityEventTrait;
}
