<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\DraftRemovedActivityEventTrait;

class DraftRemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use DraftRemovedActivityEventTrait;
}
