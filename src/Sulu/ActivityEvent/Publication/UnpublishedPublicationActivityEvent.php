<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\UnpublishedActivityEventTrait;

class UnpublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use UnpublishedActivityEventTrait;
}
