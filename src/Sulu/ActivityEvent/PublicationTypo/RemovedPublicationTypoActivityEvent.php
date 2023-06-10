<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\PublicationTypo;

use App\Sulu\ActivityEvent\Common\RemovedActivityEventTrait;

class RemovedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RemovedActivityEventTrait;
}
