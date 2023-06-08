<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\PublishedActivityEventTrait;

class PublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use PublishedActivityEventTrait;
}
