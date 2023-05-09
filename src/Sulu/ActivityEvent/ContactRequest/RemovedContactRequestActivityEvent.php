<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\ContactRequest;

use App\Sulu\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RemovedActivityEventTrait;
}
