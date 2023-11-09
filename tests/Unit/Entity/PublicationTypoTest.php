<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Publication;
use App\Entity\PublicationTypo;
use PHPUnit\Framework\TestCase;

class PublicationTypoTest extends TestCase
{
    public function testMethods(): void
    {
        $publication = new Publication();
        $description = 'description';
        $typo = new PublicationTypo(
            $publication,
            $description,
        );

        $this->assertSame($publication, $typo->getPublication());
        $this->assertSame($description, $typo->getDescription());
    }
}
