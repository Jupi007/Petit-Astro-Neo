<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Common;

use App\Tests\Implementation\Entity\Common\LocalizableAuditableTraitImplementation;
use PHPUnit\Framework\TestCase;
use Sulu\Component\Security\Authentication\UserInterface;

class LocalizableAuditableTraitTest extends TestCase
{
    public function testMethods(): void
    {
        $locale = 'fr';
        $creator = $this->createUserMock();
        $changer = $this->createUserMock();
        $created = new \DateTime();
        $changed = new \DateTime();

        $class = (new LocalizableAuditableTraitImplementation())
            ->setLocale($locale)
            ->setCreator($creator)
            ->setChanger($changer)
            ->setCreated($created)
            ->setChanged($changed);

        $this->assertSame($locale, $class->getLocale());
        $this->assertSame($creator, $class->getCreator());
        $this->assertSame($changer, $class->getChanger());
        $this->assertSame($created, $class->getCreated());
        $this->assertSame($changed, $class->getChanged());
    }

    private function createUserMock(): UserInterface
    {
        return $this->createMock(UserInterface::class);
    }
}
