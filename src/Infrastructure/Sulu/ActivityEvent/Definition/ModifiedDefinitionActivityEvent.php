<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Definition;

use App\Infrastructure\Sulu\ActivityEvent\Trait\ModifiedActivityEventTrait;

class ModifiedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use ModifiedActivityEventTrait;
}
