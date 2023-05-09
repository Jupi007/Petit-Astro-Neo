<?php

declare(strict_types=1);

namespace App\ActivityEvent\PublicationTypo;

use App\ActivityEvent\Trait\RemovedActivityEventTrait;

class RemovedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RemovedActivityEventTrait;
}
