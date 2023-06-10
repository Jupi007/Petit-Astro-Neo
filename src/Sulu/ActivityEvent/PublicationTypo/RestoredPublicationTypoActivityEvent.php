<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\PublicationTypo;

use App\Sulu\ActivityEvent\Common\RestoredActivityEventTrait;

class RestoredPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RestoredActivityEventTrait;
}
