<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Infrastructure\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RestoredActivityEventTrait;
}
