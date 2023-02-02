<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Event\Trait\CreatedActivityEventTrait;

class CreatedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use CreatedActivityEventTrait;
}
