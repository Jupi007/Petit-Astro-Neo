<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\RemovedActivityEventTrait;

class RemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RemovedActivityEventTrait;
}
