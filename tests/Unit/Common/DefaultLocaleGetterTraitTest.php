<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Tests\Implementation\Common\DefaultLocaleGetterTraitImplementation;
use PHPUnit\Framework\TestCase;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class DefaultLocaleGetterTraitTest extends TestCase
{
    public function testGetDefaultLocale(): void
    {
        $localization = [
            $this->createLocalizationMock('en', false),
            $this->createLocalizationMock('fr', true),
        ];

        $class = $this->createDefaultLocaleGetterImplementation($localization);

        $this->assertSame('fr', $class->getDefaultLocaleForTesting());
    }

    public function testGetDefaultLocaleNoDefaultLocalization(): void
    {
        $class = $this->createDefaultLocaleGetterImplementation([]);

        $this->expectException(\LogicException::class);

        $class->getDefaultLocaleForTesting();
    }

    /** @param array<Localization> $localizations */
    private function createDefaultLocaleGetterImplementation(array $localizations): DefaultLocaleGetterTraitImplementation
    {
        $webspaceManager = $this->createMock(WebspaceManagerInterface::class);
        $webspaceManager->method('getAllLocalizations')->willReturn($localizations);

        return new DefaultLocaleGetterTraitImplementation($webspaceManager);
    }

    private function createLocalizationMock(
        string $locale,
        bool $isDefault,
    ): Localization {
        $localization = $this->createMock(Localization::class);
        $localization->method('isDefault')->willReturn($isDefault);
        $localization->method('getLocale')->willReturn($locale);

        return $localization;
    }
}
