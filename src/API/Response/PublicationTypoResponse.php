<?php

declare(strict_types=1);

namespace App\API\Response;

use App\Entity\PublicationTypo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

#[ExclusionPolicy(ExclusionPolicy::ALL)]
class PublicationTypoResponse
{
    public function __construct(
        private readonly PublicationTypo $typo,
    ) {
    }

    #[VirtualProperty]
    #[SerializedName('description')]
    public function getDescription(): string
    {
        return $this->typo->getDescription();
    }

    #[VirtualProperty]
    #[SerializedName('publication')]
    public function getPublication(): ?int
    {
        return $this->typo->getPublication()->getId();
    }
}
