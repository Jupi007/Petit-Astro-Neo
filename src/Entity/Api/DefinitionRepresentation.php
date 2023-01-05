<?php

declare(strict_types=1);

namespace App\Entity\Api;

use App\Entity\Definition;

class DefinitionRepresentation
{
    private function __construct(
        private ?int $id = null,
        private ?string $title = null,
        private ?string $content = null,
        private ?string $locale = null,
    ) {
    }

    public static function fromDefinition(Definition $definition): self
    {
        return new self(
            id: $definition->getId(),
            title: $definition->getTitle(),
            content: $definition->getContent(),
            locale: $definition->getLocale(),
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
