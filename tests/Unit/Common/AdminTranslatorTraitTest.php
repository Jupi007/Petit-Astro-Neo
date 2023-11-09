<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Common\AdminTranslatorTrait;
use App\Tests\Implementation\Common\AdminTranslatorTraitImplementation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminTranslatorTraitTest extends TestCase
{
    public function testTrans(): void
    {
        $translatedString = 'translated string';
        $id = 'id';

        $webspaceManager = $this->createMock(Translator::class);
        $webspaceManager->method('trans')->with($id, [], 'admin')->willReturn($translatedString);

        $class = new AdminTranslatorTraitImplementation($webspaceManager);

        $this->assertSame($translatedString, $class->transForTesting($id));
    }
}
