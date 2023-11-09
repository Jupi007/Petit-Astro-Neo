<?php

declare(strict_types=1);

namespace App\Tests\Implementation\Common;

use App\Common\AdminTranslatorTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminTranslatorTraitImplementation
{
    use AdminTranslatorTrait;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function transForTesting(string $id): string
    {
        return $this->trans($id);
    }
}
