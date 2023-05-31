<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\PublicationTypo;

use App\Infrastructure\Sulu\ActivityEvent\Trait\CreatedActivityEventTrait;

class CreatedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use CreatedActivityEventTrait;
}
