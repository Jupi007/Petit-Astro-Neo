<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\UnpublishedActivityEventTrait;

class UnpublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use UnpublishedActivityEventTrait;
}
