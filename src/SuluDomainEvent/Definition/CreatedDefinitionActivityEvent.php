<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Definition;

use App\SuluDomainEvent\Trait\CreatedActivityEventTrait;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use CreatedActivityEventTrait;
}
