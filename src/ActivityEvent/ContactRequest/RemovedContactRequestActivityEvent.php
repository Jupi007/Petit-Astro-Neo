<?php

declare(strict_types=1);

namespace App\ActivityEvent\ContactRequest;

use App\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RemovedActivityEventTrait;
}
