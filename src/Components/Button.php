<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
    use ComponentVariantTrait;

    public bool $bordered = true;

    public bool $small = false;
}
