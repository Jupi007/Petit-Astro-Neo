<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RemovedActivityEventTrait;
}
