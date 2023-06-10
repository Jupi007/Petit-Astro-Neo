<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Common\DraftRemovedActivityEventTrait;

class DraftRemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use DraftRemovedActivityEventTrait;
}
