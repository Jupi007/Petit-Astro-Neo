<?php

declare(strict_types=1);

namespace App\API\Representation;

use App\Entity\Definition;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

#[ExclusionPolicy(ExclusionPolicy::ALL)]
class DefinitionRepresentation
{
    public function __construct(
        private readonly Definition $definition,
    ) {
    }

    #[VirtualProperty]
    #[SerializedName('id')]
    public function getId(): ?int
    {
        return $this->definition->getId();
    }

    #[VirtualProperty]
    #[SerializedName('locale')]
    public function getLocale(): ?string
    {
        return $this->definition->getLocale();
    }

    /** @return string[] */
    #[VirtualProperty]
    #[SerializedName('availableLocales')]
    public function getAvailableLocales(): array
    {
        return $this->definition->getLocales();
    }

    #[VirtualProperty]
    #[SerializedName('title')]
    public function getTitle(): ?string
    {
        return $this->definition->getTitle();
    }

    #[VirtualProperty]
    #[SerializedName('description')]
    public function getDescription(): ?string
    {
        return $this->definition->getDescription();
    }

    #[VirtualProperty]
    #[SerializedName('routePath')]
    public function getRoutePath(): ?string
    {
        return $this->definition->getRoute()?->getPath();
    }
}
