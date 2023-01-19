<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\RemovedActivityEventTrait;

class RemovedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use RemovedActivityEventTrait;
}
