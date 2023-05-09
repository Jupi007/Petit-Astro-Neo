<?php

declare(strict_types=1);

namespace App\ActivityEvent\PublicationTypo;

use App\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use CreatedActivityEventTrait;
}
