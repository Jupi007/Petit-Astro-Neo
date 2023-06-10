<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Common\RestoredActivityEventTrait;

class RestoredPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RestoredActivityEventTrait;
}
