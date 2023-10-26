<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('clickable-image')]
class ClickableImage
{
    /** @var object|mixed[] */
    public object|array $image;
}
