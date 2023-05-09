<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Definition;

use App\SuluDomainEvent\Trait\RemovedActivityEventTrait;

class RemovedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use RemovedActivityEventTrait;
}
