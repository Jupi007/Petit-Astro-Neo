<?php

declare(strict_types=1);

namespace App\SmartContent\DataItem;

use App\Entity\Definition;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use Sulu\Component\SmartContent\ItemInterface;

class DefinitionDataItem implements ItemInterface
{
    public function __construct(
        #[Exclude]
        private readonly Definition $definition,
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
        return (string) $this->definition->getId();
    }

    #[VirtualProperty]
    public function getTitle()
    {
        return $this->definition->getTitle() ?? '';
    }

    #[VirtualProperty]
    public function getImage(): string
    {
        return '';
    }
}
