<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Publication;

use App\SuluDomainEvent\Trait\ModifiedActivityEventTrait;

class ModifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use ModifiedActivityEventTrait;
}
