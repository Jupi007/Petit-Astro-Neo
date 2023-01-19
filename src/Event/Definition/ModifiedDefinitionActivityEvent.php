<?php

declare(strict_types=1);

namespace App\Event\Definition;

use App\Event\Trait\ModifiedActivityEventTrait;

class ModifiedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use ModifiedActivityEventTrait;
}
