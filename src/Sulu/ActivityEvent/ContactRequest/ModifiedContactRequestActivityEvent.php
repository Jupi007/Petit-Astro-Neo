<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\ContactRequest;

use App\Sulu\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use ModifiedActivityEventTrait;
}
