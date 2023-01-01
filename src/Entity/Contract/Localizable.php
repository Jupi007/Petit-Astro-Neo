<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface Localizable
{
    public function getLocale(): string;

    public function setLocale(string $locale): self;

    /**
     * @return object[]
     */
    public function getTranslations(): array;
}
