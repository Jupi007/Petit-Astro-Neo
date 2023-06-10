<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\Definition;

use App\Sulu\ActivityEvent\Common\CreatedActivityEventTrait;

class CreatedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use CreatedActivityEventTrait;
}
