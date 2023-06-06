<?php

declare(strict_types=1);

namespace App\Domain\Entity\Contract;

interface EntityTranslationInterface
{
    public function getLocale(): string;
}
