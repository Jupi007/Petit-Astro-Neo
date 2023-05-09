<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Definition;

use App\SuluDomainEvent\Trait\ModifiedActivityEventTrait;

class ModifiedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use ModifiedActivityEventTrait;
}
