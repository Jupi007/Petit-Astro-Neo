<?php

declare(strict_types=1);

namespace App\Entity\Contract;

interface TranslationInterface
{
    public function getLocale(): string;
}
