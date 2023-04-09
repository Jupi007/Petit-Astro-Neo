<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\NotifiedActivityEventTrait;

class NotifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use NotifiedActivityEventTrait;
}
