<?php

declare(strict_types=1);

namespace App\Tests\Unit\API\Response;

use App\API\Response\PublicationTypoResponse;
use App\Entity\Publication;
use App\Entity\PublicationTypo;
use PHPUnit\Framework\TestCase;

class PublicationTypoResponseTest extends TestCase
{
    /** @return mixed[] */
    public function dataProviderForResponseMethods(): array
    {
        return [
            ['getDescription', 'Sample Description'],
            ['getPublication', 123],
        ];
    }

    /** @dataProvider dataProviderForResponseMethods */
    public function testResponseMethods(string $method, mixed $expectedValue): void
    {
        $response = $this->getConfiguredPublicationTypoResponse($method, $expectedValue);

        $this->assertSame($expectedValue, $response->{$method}());
    }

    private function getConfiguredPublicationTypoResponse(
        string $method,
        mixed $returnValue,
    ): PublicationTypoResponse {
        $typo = $this->createMock(PublicationTypo::class);

        if ('getPublication' === $method) {
            $publication = $this->createMock(Publication::class);
            $publication->method('getId')->willReturn($returnValue);
            $typo->method('getPublication')->willReturn($publication);
        } else {
            $typo->method($method)->willReturn($returnValue);
        }

        return new PublicationTypoResponse($typo);
    }
}
