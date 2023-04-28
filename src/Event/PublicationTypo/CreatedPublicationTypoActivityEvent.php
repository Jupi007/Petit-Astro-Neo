<?php

declare(strict_types=1);

namespace App\Event\PublicationTypo;

use App\Event\Trait\CreatedActivityEventTrait;

class CreatedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use CreatedActivityEventTrait;
}
