<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\PublicationTypo;

use App\SuluDomainEvent\Trait\CreatedActivityEventTrait;

class CreatedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use CreatedActivityEventTrait;
}
