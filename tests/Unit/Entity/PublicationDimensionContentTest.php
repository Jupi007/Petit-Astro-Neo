<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use PHPUnit\Framework\TestCase;

class PublicationDimensionContentTest extends TestCase
{
    public function testMethods(): void
    {
        $publication = new Publication();
        $title = 'title';
        $dimensionContent = new PublicationDimensionContent($publication);
        $dimensionContent->setTemplateData(['title' => $title]);

        $this->assertSame($publication, $dimensionContent->getResource());
        $this->assertSame($title, $dimensionContent->getTitle());
    }
}
