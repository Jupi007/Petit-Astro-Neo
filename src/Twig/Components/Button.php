<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Twig\Components\Common\ComponentVariantTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('button')]
class Button
{
    use ComponentVariantTrait;

    public bool $bordered = true;

    public bool $small = false;
}
