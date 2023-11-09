<?php

declare(strict_types=1);

namespace App\Tests\Unit\Event;

use App\Entity\PublicationTypo;
use App\Event\PublicationTypo\AbstractPublicationTypoEvent;
use PHPUnit\Framework\TestCase;

class PublicationTypoEventTest extends TestCase
{
    public function testAbstractPublicationTypoEvent(): void
    {
        $typo = $this->createPublicationTypoMock();
        $event = $this->createAbstractPublicationTypoEvent($typo);

        $this->assertSame($typo, $event->getResource());
    }

    private function createAbstractPublicationTypoEvent(PublicationTypo $typo): AbstractPublicationTypoEvent
    {
        return new class($typo) extends AbstractPublicationTypoEvent { };
    }

    private function createPublicationTypoMock(): PublicationTypo
    {
        return $this->createMock(PublicationTypo::class);
    }
}
