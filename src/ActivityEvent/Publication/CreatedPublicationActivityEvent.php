<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use CreatedActivityEventTrait;
}
