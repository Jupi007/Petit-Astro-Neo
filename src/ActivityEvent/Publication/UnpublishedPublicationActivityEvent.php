<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\UnpublishedActivityEventTrait;

class UnpublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use UnpublishedActivityEventTrait;
}
