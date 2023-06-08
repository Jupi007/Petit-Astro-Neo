<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use ModifiedActivityEventTrait;
}
