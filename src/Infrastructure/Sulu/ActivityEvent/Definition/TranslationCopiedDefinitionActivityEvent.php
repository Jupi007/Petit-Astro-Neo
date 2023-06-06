<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Definition;

use App\Domain\Entity\Definition;
use App\Infrastructure\Sulu\ActivityEvent\Trait\TranslationCopiedActivityEventTrait;

class TranslationCopiedDefinitionActivityEvent extends AbstractDefinitionActivityEvent
{
    use TranslationCopiedActivityEventTrait;

    public function __construct(
        Definition $definition,
        private readonly string $srcLocale,
        private readonly string $destLocale,
    ) {
        parent::__construct($definition);
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
