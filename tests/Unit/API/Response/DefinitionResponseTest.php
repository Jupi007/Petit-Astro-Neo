<?php

declare(strict_types=1);

namespace App\Tests\Unit\API\Response;

use App\API\Response\DefinitionResponse;
use App\Entity\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionResponseTest extends TestCase
{
    /** @return mixed[] */
    public function dataProvider(): array
    {
        return [
            ['getId', 123],
            ['getLocale', 'en'],
            ['getAvailableLocales', ['en', 'fr', 'de']],
            ['getTitle', 'Sample Title'],
            ['getDescription', 'Sample Description'],
            ['getRoutePath', '/sample/route'],
        ];
    }

    /** @dataProvider dataProvider */
    public function testMethods(string $method, mixed $expectedValue): void
    {
        $response = $this->getConfiguredDefinitionResponse($method, $expectedValue);

        $this->assertSame($expectedValue, $response->{$method}());
    }

    private function getConfiguredDefinitionResponse(
        string $method,
        mixed $returnValue,
    ): DefinitionResponse {
        $definition = $this->createMock(Definition::class);
        $definition->method($method)->willReturn($returnValue);

        return new DefinitionResponse($definition);
    }
}
