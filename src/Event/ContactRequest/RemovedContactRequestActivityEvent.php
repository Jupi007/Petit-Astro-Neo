<?php

declare(strict_types=1);

namespace App\Event\ContactRequest;

use App\Event\Trait\RemovedActivityEventTrait;

class RemovedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use RemovedActivityEventTrait;
}
