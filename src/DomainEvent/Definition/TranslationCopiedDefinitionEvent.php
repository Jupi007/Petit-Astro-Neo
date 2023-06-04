<?php

declare(strict_types=1);

namespace App\DomainEvent\Definition;

use App\Entity\Definition;

class TranslationCopiedDefinitionEvent extends AbstractDefinitionEvent
{
    public function __construct(
        Definition $resource,
        private readonly string $srcLocale,
        private readonly string $destLocale,
    ) {
        parent::__construct($resource);
    }

    public function getSrcLocale(): string
    {
        return $this->srcLocale;
    }

    public function getDestLocale(): string
    {
        return $this->destLocale;
    }
}
