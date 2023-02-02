<?php

declare(strict_types=1);

namespace App\Event\Definition;

use App\Event\Trait\RestoredActivityEventTrait;

class RestoredDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RestoredActivityEventTrait;
}
