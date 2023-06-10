<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Definition;

use App\Sulu\ActivityEvent\Common\ModifiedActivityEventTrait;

class ModifiedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use ModifiedActivityEventTrait;
}
