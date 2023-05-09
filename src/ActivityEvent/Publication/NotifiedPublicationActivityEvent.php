<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\NotifiedActivityEventTrait;

class NotifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use NotifiedActivityEventTrait;
}
