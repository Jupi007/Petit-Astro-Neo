<?php

declare(strict_types=1);

namespace App\Event\PublicationTypo;

use App\Event\Trait\RestoredActivityEventTrait;

class RestoredPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RestoredActivityEventTrait;
}
