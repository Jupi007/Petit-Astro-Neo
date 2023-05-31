<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Infrastructure\Sulu\ActivityEvent\Trait\NotifiedActivityEventTrait;

class NotifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use NotifiedActivityEventTrait;
}
