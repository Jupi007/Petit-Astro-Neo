<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\PublishedActivityEventTrait;

class PublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use PublishedActivityEventTrait;
}
