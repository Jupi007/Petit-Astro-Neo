<?php

declare(strict_types=1);

namespace App\Tests\Unit\Event;

use App\Entity\NewsletterRegistration;
use App\Event\NewsletterRegistration\AbstractNewsletterRegistrationEvent;
use PHPUnit\Framework\TestCase;

class NewsletterRegistrationTest extends TestCase
{
    public function testAbstractNewsletterRegistrationEvent(): void
    {
        $registration = $this->createNewsletterRegistrationMock();
        $event = $this->createAbstractNewsletterRegistrationEvent($registration);

        $this->assertSame($registration, $event->getResource());
    }

    private function createAbstractNewsletterRegistrationEvent(NewsletterRegistration $registration): AbstractNewsletterRegistrationEvent
    {
        return new class($registration) extends AbstractNewsletterRegistrationEvent { };
    }

    private function createNewsletterRegistrationMock(): NewsletterRegistration
    {
        return $this->createMock(NewsletterRegistration::class);
    }
}
