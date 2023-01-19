<?php

declare(strict_types=1);

namespace App\SmartContent\DataItem;

use App\Entity\Publication;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use Sulu\Component\SmartContent\ItemInterface;

class PublicationDataItem implements ItemInterface
{
    public function __construct(
        #[Exclude]
        private readonly Publication $publication,
    ) {
    }

    #[VirtualProperty]
    public function getResource(): null
    {
        return null;
    }

    #[VirtualProperty]
    public function getId(): string
    {
        return (string) $this->publication->getId();
    }

    #[VirtualProperty]
    public function getTitle()
    {
        return $this->publication->getTitle() ?? '';
    }

    #[VirtualProperty]
    public function getImage(): string
    {
        return '';
    }
}
