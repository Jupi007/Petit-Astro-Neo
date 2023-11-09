<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Common;

use App\Tests\Implementation\Entity\Common\PersistableEntityTraitImplementation;
use PHPUnit\Framework\TestCase;

class PersistableEntityTraitTest extends TestCase
{
    public function testGetId(): void
    {   
        $id = 123;
        $class = (new PersistableEntityTraitImplementation())
            ->setId($id);

        $this->assertSame($id, $class->getId());
    }
}
