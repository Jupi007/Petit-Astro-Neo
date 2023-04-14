<?php

declare(strict_types=1);

namespace App\Event\ContactRequest;

use App\Event\Trait\ModifiedActivityEventTrait;

class ModifiedContactRequestActivityEvent extends AbstractContactRequestActivityEvent
{
    use ModifiedActivityEventTrait;
}
