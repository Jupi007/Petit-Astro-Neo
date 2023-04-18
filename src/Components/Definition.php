<?php

declare(strict_types=1);

namespace App\Components;

use App\Entity\Definition as EntityDefinition;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('definition')]
class Definition
{
    public EntityDefinition $definition;
}
