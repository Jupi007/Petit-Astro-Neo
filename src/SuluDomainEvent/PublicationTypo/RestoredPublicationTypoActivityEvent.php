<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\PublicationTypo;

use App\SuluDomainEvent\Trait\RestoredActivityEventTrait;

class RestoredPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RestoredActivityEventTrait;
}
