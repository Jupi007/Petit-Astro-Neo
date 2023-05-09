<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use ModifiedActivityEventTrait;
}
