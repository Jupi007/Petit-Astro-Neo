<?php

declare(strict_types=1);

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('card')]
class Card
{
    /** @var object|mixed[]|null */
    public object|array|null $image = null;
}
