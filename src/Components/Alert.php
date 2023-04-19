<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert')]
class Alert
{
    use ComponentVariantTrait;

    public bool $demissible = false;
}
