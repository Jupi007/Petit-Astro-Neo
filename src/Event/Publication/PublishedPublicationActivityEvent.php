<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\PublishedActivityEventTrait;

class PublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use PublishedActivityEventTrait;
}
