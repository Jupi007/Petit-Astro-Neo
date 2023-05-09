<?php

declare(strict_types=1);

namespace App\TwigComponents;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('card')]
class Card
{
    /** @var object|mixed[]|null */
    public object|array|null $image = null;
}
