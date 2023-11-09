<?php

declare(strict_types=1);

namespace App\Tests\Unit\Event;

use App\Entity\Definition;
use App\Event\Definition\AbstractDefinitionEvent;
use App\Event\Definition\TranslationCopiedDefinitionEvent;
use PHPUnit\Framework\TestCase;

class DefinitionEventTest extends TestCase
{
    public function testAbstractDefinitionEvent(): void
    {
        $definition = $this->createDefinitionMock();
        $event = $this->createAbstractDefinitionEvent($definition);

        $this->assertSame($definition, $event->getResource());
    }

    public function testTranslationCopiedDefinitionEvent(): void
    {
        $definition = $this->createDefinitionMock();
        $srcLocale = 'fr';
        $destLocale = 'en';
        $event = new TranslationCopiedDefinitionEvent($definition, $srcLocale, $destLocale);

        $this->assertSame($srcLocale, $event->getSrcLocale());
        $this->assertSame($destLocale, $event->getDestLocale());
    }

    private function createAbstractDefinitionEvent(Definition $definition): AbstractDefinitionEvent
    {
        return new class($definition) extends AbstractDefinitionEvent { };
    }

    private function createDefinitionMock(): Definition
    {
        return $this->createMock(Definition::class);
    }
}
