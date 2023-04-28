<?php

declare(strict_types=1);

namespace App\Common;

use Symfony\Contracts\Translation\TranslatorInterface;

trait AdminTranslatorTrait
{
    public function trans(string $id): string
    {
        return $this->getTranslator()->trans($id, domain: 'admin');
    }

    abstract protected function getTranslator(): TranslatorInterface;
}
