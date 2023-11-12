<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository\Doctrine;

use App\Tests\Application\Entity\LocalizedTestEntity;
use App\Tests\Application\Exception\NotFoundEntityException;
use App\Tests\Application\Repository\Doctrine\LocalizedRepositoryTraitImplementation;
use App\Tests\Common\DoctrineRepositoryTestCase;

class LocalizedRepositoryTraitTest extends DoctrineRepositoryTestCase
{
    public function testFindOneLocalized(): void
    {
        $expectedLocale = 'fr';
        $entity = new LocalizedTestEntity();
        $entity
            ->setLocale('fr')
            ->setTitle('title fr')
            ->setLocale('en')
            ->setTitle('title en');

        self::persistAndFlush($entity);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->findOneLocalized($entity->getId(), $expectedLocale);

        $this->assertSame($entity, $entityFromDatabase);
        $this->assertSame($expectedLocale, $entityFromDatabase->getLocale());
    }

    public function testGetOneLocalized(): void
    {
        $expectedLocale = 'fr';
        $entity = new LocalizedTestEntity();
        $entity
            ->setLocale('fr')
            ->setTitle('title fr')
            ->setLocale('en')
            ->setTitle('title en');

        self::persistAndFlush($entity);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->getOneLocalized($entity->getId(), $expectedLocale);

        $this->assertSame($entity, $entityFromDatabase);
        $this->assertSame($expectedLocale, $entityFromDatabase->getLocale());
    }

    public function testGetOneLocalizedNotFound(): void
    {
        $this->expectException(NotFoundEntityException::class);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $repository->getOneLocalized(1, 'fr');
    }

    public function testFindOneLocalizedBy(): void
    {
        $locale = 'fr';

        $entity1 = new LocalizedTestEntity(published: false);
        $entity2 = new LocalizedTestEntity(published: true);
        $entity2
            ->setLocale($locale)
            ->setTitle('title');

        self::persistAndFlush($entity1);
        self::persistAndFlush($entity2);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->findOneLocalizedBy(['published' => true], $locale);

        $this->assertSame($entity2, $entityFromDatabase);
        $this->assertSame($locale, $entityFromDatabase->getLocale());
    }

    public function testGetOneLocalizedBy(): void
    {
        $locale = 'fr';

        $entity1 = new LocalizedTestEntity(published: false);
        $entity2 = new LocalizedTestEntity(published: true);
        $entity2
            ->setLocale($locale)
            ->setTitle('title');

        self::persistAndFlush($entity1);
        self::persistAndFlush($entity2);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->getOneLocalizedBy(['published' => true], $locale);

        $this->assertSame($entity2, $entityFromDatabase);
        $this->assertSame($locale, $entityFromDatabase->getLocale());
    }

    public function testGetOneLocalizedByNotFound(): void
    {
        $entity = new LocalizedTestEntity(published: false);

        self::persistAndFlush($entity);

        $this->expectException(NotFoundEntityException::class);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $repository->getOneLocalizedBy(['published' => true], 'fr');
    }

    public function testFindAllLocalized(): void
    {
        $locale = 'fr';

        $entity1 = new LocalizedTestEntity(published: true);
        $entity2 = new LocalizedTestEntity(published: true);

        self::persistAndFlush($entity1);
        self::persistAndFlush($entity2);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        $entitiesFromDatabase = $repository->findAllLocalized($locale);

        $this->assertCount(2, $entitiesFromDatabase);
        foreach ($entitiesFromDatabase as $entity) {
            $this->assertSame($locale, $entity->getLocale());
        }
    }

    public function testFindLocalizedBy(): void
    {
        $locale = 'fr';

        $entity1 = new LocalizedTestEntity(published: false);
        $entity2 = new LocalizedTestEntity(published: true);
        $entity3 = new LocalizedTestEntity(published: true);
        $entity4 = new LocalizedTestEntity(published: true);
        $entity5 = new LocalizedTestEntity(published: true);
        $entity6 = new LocalizedTestEntity(published: false);

        self::persistAndFlush($entity1);
        self::persistAndFlush($entity2);
        self::persistAndFlush($entity3);
        self::persistAndFlush($entity4);
        self::persistAndFlush($entity5);
        self::persistAndFlush($entity6);

        $repository = new LocalizedRepositoryTraitImplementation(self::getEntityManager());
        /** @var LocalizedTestEntity[] */
        $entitiesFromDatabase = $repository->findLocalizedBy(
            criteria: ['published' => true],
            locale: $locale,
            orderBy: ['id' => 'desc'],
            limit: 2,
            offset: 1,
        );

        $this->assertCount(2, $entitiesFromDatabase);
        $this->assertSame($entity4->getId(), $entitiesFromDatabase[0]->getId());
        $this->assertSame($entity3->getId(), $entitiesFromDatabase[1]->getId());
        foreach ($entitiesFromDatabase as $entity) {
            $this->assertSame($locale, $entity->getLocale());
        }
    }
}
