<?php

declare(strict_types=1);

namespace App\ActivityEvent\PublicationTypo;

use App\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RestoredActivityEventTrait;
}
