<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RemovedActivityEventTrait;
}
