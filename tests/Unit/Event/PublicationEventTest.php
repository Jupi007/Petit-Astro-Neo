<?php

declare(strict_types=1);

namespace App\Tests\Unit\Event;

use App\Entity\Publication;
use App\Event\Publication\AbstractPublicationEvent;
use App\Event\Publication\TranslationCopiedPublicationEvent;
use PHPUnit\Framework\TestCase;

class PublicationEventTest extends TestCase
{
    public function testAbstractPublicationEvent(): void
    {
        $publication = $this->createPublicationMock();
        $event = $this->createAbstractPublicationEvent($publication);

        $this->assertSame($publication, $event->getResource());
    }

    public function testTranslationCopiedPublicationEvent(): void
    {
        $publication = $this->createPublicationMock();
        $srcLocale = 'fr';
        $destLocale = 'en';
        $event = new TranslationCopiedPublicationEvent($publication, $srcLocale, $destLocale);

        $this->assertSame($srcLocale, $event->getSrcLocale());
        $this->assertSame($destLocale, $event->getDestLocale());
    }

    private function createAbstractPublicationEvent(Publication $publication): AbstractPublicationEvent
    {
        return new class($publication) extends AbstractPublicationEvent { };
    }

    private function createPublicationMock(): Publication
    {
        return $this->createMock(Publication::class);
    }
}
