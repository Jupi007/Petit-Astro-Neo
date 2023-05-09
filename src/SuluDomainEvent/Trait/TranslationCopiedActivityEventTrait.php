<?php

declare(strict_types=1);

namespace App\SuluDomainEvent\Trait;

use App\SuluDomainEvent\ActivityEventType;

trait TranslationCopiedActivityEventTrait
{
    public function getActivityEventType(): ActivityEventType
    {
        return ActivityEventType::TranslationCopied;
    }

    public function getEventContext(): array
    {
        return [
            'sourceLocale' => $this->getSrcLocale(),
        ];
    }

    public function getResourceLocale(): ?string
    {
        return $this->getDestLocale();
    }

    abstract public function getSrcLocale(): string;

    abstract public function getDestLocale(): string;
}
