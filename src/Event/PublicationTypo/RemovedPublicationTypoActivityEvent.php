<?php

declare(strict_types=1);

namespace App\Event\PublicationTypo;

use App\Event\Trait\RemovedActivityEventTrait;

class RemovedPublicationTypoActivityEvent extends AbstractPublicationTypoActivityEvent
{
    use RemovedActivityEventTrait;
}
