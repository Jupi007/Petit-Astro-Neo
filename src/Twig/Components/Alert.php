<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Twig\Components\Common\ComponentVariantTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert')]
class Alert
{
    use ComponentVariantTrait;

    public bool $demissible = false;
}
