<?php

declare(strict_types=1);

namespace App\Entity\Api;

use App\Entity\Publication;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

#[ExclusionPolicy(ExclusionPolicy::ALL)]
class PublicationRepresentation
{
    public function __construct(
        private readonly Publication $publication,
    ) {
    }

    #[VirtualProperty]
    #[SerializedName('id')]
    public function getId(): ?int
    {
        return $this->publication->getId();
    }

    #[VirtualProperty]
    #[SerializedName('locale')]
    public function getLocale(): ?string
    {
        return $this->publication->getLocale();
    }

    #[VirtualProperty]
    #[SerializedName('title')]
    public function getTitle(): ?string
    {
        return $this->publication->getTitle();
    }

    #[VirtualProperty]
    #[SerializedName('subtitle')]
    public function getSubtitle(): ?string
    {
        return $this->publication->getSubtitle();
    }

    /** @return mixed[] */
    #[VirtualProperty]
    #[SerializedName('blocks')]
    public function getBlocks(): ?array
    {
        return $this->publication->getBlocks();
    }

    #[VirtualProperty]
    #[SerializedName('routePath')]
    public function getRoutePath(): ?string
    {
        return $this->publication->getRoute()?->getPath();
    }
}
