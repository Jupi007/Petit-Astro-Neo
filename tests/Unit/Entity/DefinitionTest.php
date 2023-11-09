<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    public function testMethods(): void
    {
        $locale = 'fr';
        $title = 'title';
        $description = 'description';
        $routePath = 'routePath';

        $definition = (new Definition())
            ->setLocale($locale)
            ->setTitle($title)
            ->setDescription($description)
            ->setRoutePath($routePath);

        $this->assertSame($locale, $definition->getLocale());
        $this->assertSame($title, $definition->getTitle());
        $this->assertSame($description, $definition->getDescription());
        $this->assertSame($routePath, $definition->getRoutePath());
    }
}
