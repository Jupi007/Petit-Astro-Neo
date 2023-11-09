<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Publication;
use App\Entity\PublicationTypo;
use PHPUnit\Framework\TestCase;

class PublicationTest extends TestCase
{
    public function testMethods(): void
    {
        $publication = (new Publication())
            ->setNotified(true);

        $typo = new PublicationTypo($publication, '');

        $this->assertTrue($publication->isNotified());
        $this->assertSame([$typo], $publication->getTypos()->toArray());
    }
}
