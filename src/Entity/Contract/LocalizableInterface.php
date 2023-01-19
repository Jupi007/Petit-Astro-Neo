<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface LocalizableInterface
{
    public function getLocale(): string;

    /** @return static */
    public function setLocale(string $locale): self;

    /** @return string[] */
    public function getLocales(): array;
}
