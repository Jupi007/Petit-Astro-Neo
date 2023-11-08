<?php

declare(strict_types=1);

namespace App\Common;

use Symfony\Contracts\Translation\TranslatorInterface;

trait AdminTranslatorTrait
{
    private readonly TranslatorInterface $translator;

    private function trans(string $id): string
    {
        return $this->translator->trans($id, domain: 'admin');
    }
}
