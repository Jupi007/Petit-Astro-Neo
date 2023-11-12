<?php

declare(strict_types=1);

namespace App\Tests\Common;

use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\TestBundle\Testing\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineRepositoryTestCase extends KernelTestCase
{
    use PurgeDatabaseTrait;

    private static ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        self::$entityManager = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager');
        self::purgeDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        self::$entityManager?->close();
        self::$entityManager = null;
    }

    public function persistAndFlush(object $entity): void
    {
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();
    }

    public static function getEntityManager(): EntityManagerInterface
    {
        return self::$entityManager ?? throw new \LogicException();
    }
}
