<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Common;

use App\Tests\Application\Entity\Common\LocalizableEntityTraitImplementation;
use PHPUnit\Framework\TestCase;

class LocalizableEntityTraitTest extends TestCase
{
    public function testMethods(): void
    {
        $class = new LocalizableEntityTraitImplementation();
        $locale = 'fr';

        $class->setLocale($locale);
        $this->assertSame($locale, $class->getLocale());

        $newTranslation = $class->getTranslation();
        $translationFromCollection = $class->getTranslation();
        $this->assertSame($locale, $newTranslation->getLocale());
        $this->assertSame($locale, $translationFromCollection->getLocale());

        $newLocale = 'en';
        $class->setLocale($newLocale);
        $class->getTranslation();
        $this->assertSame([$locale, $newLocale], $class->getLocales());
    }
}
