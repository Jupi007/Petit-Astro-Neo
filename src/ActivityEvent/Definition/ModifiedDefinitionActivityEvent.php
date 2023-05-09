<?php

declare(strict_types=1);

namespace App\ActivityEvent\Definition;

use App\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use ModifiedActivityEventTrait;
}
