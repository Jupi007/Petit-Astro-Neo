<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\PublicationTypo;

use App\Infrastructure\Sulu\ActivityEvent\Trait\RestoredActivityEventTrait;

class RestoredPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RestoredActivityEventTrait;
}
