<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface EntityTranslationInterface
{
    public function getLocale(): string;
}
