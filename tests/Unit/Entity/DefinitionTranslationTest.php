<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Definition;
use App\Entity\DefinitionTranslation;
use PHPUnit\Framework\TestCase;

class DefinitionTranslationTest extends TestCase
{
    public function testMethods(): void
    {
        $definition = new Definition();
        $title = 'title';
        $description = 'description';
        $routePath = '/routePath';

        $translation = (new DefinitionTranslation($definition, 'fr'))
            ->setTitle($title)
            ->setDescription($description)
            ->setRoutePath($routePath);

        $this->assertSame($definition, $translation->getDefinition());
        $this->assertSame($title, $translation->getTitle());
        $this->assertSame($description, $translation->getDescription());
        $this->assertSame($routePath, $translation->getRoutePath());
    }
}
