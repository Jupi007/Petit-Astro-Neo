<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\ModifiedActivityEventTrait;

class ModifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use ModifiedActivityEventTrait;
}
