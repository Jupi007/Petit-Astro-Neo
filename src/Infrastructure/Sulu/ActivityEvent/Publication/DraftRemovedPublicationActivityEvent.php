<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Infrastructure\Sulu\ActivityEvent\Trait\DraftRemovedActivityEventTrait;

class DraftRemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use DraftRemovedActivityEventTrait;
}
