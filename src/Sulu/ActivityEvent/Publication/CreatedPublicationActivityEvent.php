<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Publication;

use App\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use CreatedActivityEventTrait;
}
