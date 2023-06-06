<?php

declare(strict_types=1);

namespace App\UserInterface\Twig\Components;

use App\Domain\Entity\Definition as EntityDefinition;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('definition')]
class Definition
{
    public string $title;
    public string $description;

    public function mount(EntityDefinition $definition = null): void
    {
        if (!$definition instanceof EntityDefinition) {
            return;
        }

        $this->title = $definition->getTitle() ?? '';
        $this->description = $definition->getDescription() ?? '';
    }
}
