<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Definition;

use App\SuluDomainEvent\Trait\RestoredActivityEventTrait;

class RestoredDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RestoredActivityEventTrait;
}
