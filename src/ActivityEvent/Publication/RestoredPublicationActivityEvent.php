<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RestoredActivityEventTrait;
}
