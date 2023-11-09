<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\NewsletterRegistration;
use PHPUnit\Framework\TestCase;

class NewsletterRegistrationTest extends TestCase
{
    public function testMethods(): void
    {
        $email = 'test@test.fr';
        $locale = 'fr';
        $registration = new NewsletterRegistration($email, $locale);

        $this->assertNotEmpty($registration->getUuid());
        $this->assertSame($email, $registration->getEmail());
        $this->assertSame($locale, $registration->getLocale());

        $locale = 'en';
        $registration->setLocale($locale);
        $this->assertSame($locale, $registration->getLocale());
    }
}
