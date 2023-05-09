<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\NotifiedActivityEventTrait;

class NotifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use NotifiedActivityEventTrait;
}
