<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Infrastructure\Sulu\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use ModifiedActivityEventTrait;
}
