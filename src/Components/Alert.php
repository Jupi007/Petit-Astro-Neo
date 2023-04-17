<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Alert
{
    use TypedComponentTrait;

    public string $label;

    public bool $demissible = false;
}
