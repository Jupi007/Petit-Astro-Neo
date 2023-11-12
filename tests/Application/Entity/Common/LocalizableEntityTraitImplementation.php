<?php

declare(strict_types=1);

namespace App\Tests\Application\Entity\Common;

use App\Entity\Common\LocalizableEntityTrait;
use App\Entity\Contract\EntityTranslationInterface;

class LocalizableEntityTraitImplementation
{
    /** @use LocalizableEntityTrait<EntityTranslationInterface> */
    use LocalizableEntityTrait;

    public function __construct()
    {
        $this->__localizableEntityTraitConstructor();
    }

    private function createTranslation(): EntityTranslationInterface
    {
        return new class() implements EntityTranslationInterface {
            public function getId(): ?int
            {
                return 1;
            }

            public function getLocale(): string
            {
                return 'fr';
            }
        };
    }
}
