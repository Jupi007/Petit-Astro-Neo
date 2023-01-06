<?php

declare(strict_types=1);

namespace App\Entity\Api;

use App\Entity\Definition;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

#[ExclusionPolicy(ExclusionPolicy::ALL)]
class DefinitionRepresentation
{
    public function __construct(
        private Definition $definition,
    ) {
    }

    #[VirtualProperty]
    #[SerializedName('id')]
    public function getId(): ?int
    {
        return $this->definition->getId();
    }

    #[VirtualProperty]
    #[SerializedName('title')]
    public function getTitle(): ?string
    {
        return $this->definition->getTitle();
    }

    #[VirtualProperty]
    #[SerializedName('content')]
    public function getContent(): ?string
    {
        return $this->definition->getContent();
    }

    #[VirtualProperty]
    #[SerializedName('locale')]
    public function getLocale(): ?string
    {
        return $this->definition->getLocale();
    }
}
