<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Tests\Unit\Common\AdminTranslatorTraitImplementation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

class AdminTranslatorTraitTest extends TestCase
{
    public function testGetDefaultLocale(): void
    {
        $translatedString = 'translated string';
        $id = 'id';

        $webspaceManager = $this->createMock(Translator::class);
        $webspaceManager->method('trans')->with($id, [], 'admin')->willReturn($translatedString);

        $class = new AdminTranslatorTraitImplementation($webspaceManager);

        $this->assertSame($translatedString, $class->transForTesting($id));
    }
}
