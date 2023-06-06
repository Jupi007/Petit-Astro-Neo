<?php

declare(strict_types=1);

namespace App\UserInterface\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('smart-content-pagination')]
class SmartContentPagination
{
    /** @var mixed[] */
    public array $view;

    public string $smartContentId;
}
