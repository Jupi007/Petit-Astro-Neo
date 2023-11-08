<?php

declare(strict_types=1);

namespace App\Tests\Unit\API\Response;

use App\API\Response\NewsletterRegistrationResponse;
use App\Entity\NewsletterRegistration;
use PHPUnit\Framework\TestCase;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\SecurityBundle\Entity\User;

class NewsletterRegistrationResponseTest extends TestCase
{
    /** @return mixed[] */
    public function dataProviderForResponseMethods(): array
    {
        return [
            ['getId', 123],
            ['getLocale', 'en'],
            ['getEmail', 'test@example.com'],
            ['getContact', 456],
        ];
    }

    /** @dataProvider dataProviderForResponseMethods */
    public function testResponseMethods(string $method, mixed $expectedValue): void
    {
        $response = $this->getConfiguredNewsletterRegistrationResponse($method, $expectedValue);

        $this->assertSame($expectedValue, $response->{$method}());
    }

    private function getConfiguredNewsletterRegistrationResponse(
        string $method,
        mixed $returnValue,
    ): NewsletterRegistrationResponse {
        $newsletterRegistration = $this->createMock(NewsletterRegistration::class);
        $user = $this->createMock(User::class);

        if ('getContact' === $method) {
            $contactMock = $this->createMock(Contact::class);
            $contactMock->method('getId')->willReturn($returnValue);
            $user->method('getContact')->willReturn($contactMock);
        } else {
            $newsletterRegistration->method($method)->willReturn($returnValue);
        }

        return new NewsletterRegistrationResponse($newsletterRegistration, $user);
    }
}
