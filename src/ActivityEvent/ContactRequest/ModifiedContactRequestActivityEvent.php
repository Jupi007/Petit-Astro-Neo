<?php

declare(strict_types=1);

namespace App\ActivityEvent\ContactRequest;

use App\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use ModifiedActivityEventTrait;
}
