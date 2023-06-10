<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\PublicationTypo;

use App\Sulu\ActivityEvent\Common\CreatedActivityEventTrait;

class CreatedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use CreatedActivityEventTrait;
}
