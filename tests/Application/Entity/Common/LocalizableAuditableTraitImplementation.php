<?php

declare(strict_types=1);

namespace App\Tests\Application\Entity\Common;

use App\Entity\Common\AuditableTrait;
use App\Entity\Common\LocalizableAuditableTrait;
use App\Entity\Common\LocalizableEntityTrait;
use App\Entity\Contract\AuditableInterface;
use App\Entity\Contract\EntityTranslationInterface;

class LocalizableAuditableTraitImplementation
{
    /** @use LocalizableAuditableTrait<EntityTranslationInterface&AuditableInterface> */
    use LocalizableAuditableTrait;
    /** @use LocalizableEntityTrait<EntityTranslationInterface&AuditableInterface> */
    use LocalizableEntityTrait;

    public function __construct()
    {
        $this->__localizableEntityTraitConstructor();
    }

    private function createTranslation(): EntityTranslationInterface&AuditableInterface
    {
        return new class() implements EntityTranslationInterface, AuditableInterface {
            use AuditableTrait;

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
