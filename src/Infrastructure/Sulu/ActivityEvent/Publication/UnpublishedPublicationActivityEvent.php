<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Infrastructure\Sulu\ActivityEvent\Trait\UnpublishedActivityEventTrait;

class UnpublishedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use UnpublishedActivityEventTrait;
}
