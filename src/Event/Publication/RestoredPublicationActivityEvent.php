<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\RestoredActivityEventTrait;

class RestoredPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RestoredActivityEventTrait;
}
