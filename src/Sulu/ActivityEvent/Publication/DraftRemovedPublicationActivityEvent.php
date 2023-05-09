<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\DraftRemovedActivityEventTrait;

class DraftRemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use DraftRemovedActivityEventTrait;
}
