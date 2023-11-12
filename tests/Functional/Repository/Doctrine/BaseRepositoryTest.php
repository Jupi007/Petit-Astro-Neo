<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository\Doctrine;

use App\Tests\Application\Entity\TestEntity;
use App\Tests\Application\Exception\NotFoundEntityException;
use App\Tests\Application\Repository\Doctrine\BaseRepositoryImplementation;
use App\Tests\Common\DoctrineRepositoryTestCase;

class BaseRepositoryTest extends DoctrineRepositoryTestCase
{
    public function testSave(): void
    {
        $entity = new TestEntity();

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $repository->save($entity);

        $entityFromDatabase = self::getEntityManager()
            ->getRepository(TestEntity::class)
            ->find($entity->getId());

        $this->assertSame($entity, $entityFromDatabase);
    }

    public function testRemove(): void
    {
        $entity = new TestEntity();

        self::persistAndFlush($entity);

        $id = $entity->getId();

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $repository->remove($entity);

        $entityFromDatabase = self::getEntityManager()
            ->getRepository(TestEntity::class)
            ->find($id);

        $this->assertNull($entityFromDatabase);
    }

    public function testFindOne(): void
    {
        $entity = new TestEntity();

        self::persistAndFlush($entity);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->findOne($entity->getId());

        $this->assertSame($entity, $entityFromDatabase);
    }

    public function testGetOne(): void
    {
        $entity = new TestEntity();

        self::persistAndFlush($entity);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->getOne($entity->getId());

        $this->assertSame($entity, $entityFromDatabase);
    }

    public function testGetOneNotFound(): void
    {
        $this->expectException(NotFoundEntityException::class);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $repository->getOne(1);
    }

    public function testFindOneBy(): void
    {
        $title = 'title';
        $entity = new TestEntity($title);

        self::persistAndFlush($entity);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->findOneBy(['title' => $title]);

        $this->assertSame($entity, $entityFromDatabase);
    }

    public function testGetOneBy(): void
    {
        $expectedTitle = 'title';
        $expectedEntity = new TestEntity($expectedTitle);
        $otherEntity = new TestEntity('other title');

        self::persistAndFlush($expectedEntity);
        self::persistAndFlush($otherEntity);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $entityFromDatabase = $repository->getOneBy(['title' => $expectedTitle]);

        $this->assertSame($expectedEntity, $entityFromDatabase);
    }

    public function testGetOneByNotFound(): void
    {
        $entity = new TestEntity('another title');

        self::persistAndFlush($entity);

        $this->expectException(NotFoundEntityException::class);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $repository->getOneBy(['title' => 'title']);
    }

    public function testFindAll(): void
    {
        $entity1 = new TestEntity();
        $entity2 = new TestEntity();

        self::persistAndFlush($entity1);
        self::persistAndFlush($entity2);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $entitiesFromDatabase = $repository->findAll();

        $this->assertCount(2, $entitiesFromDatabase);
    }

    public function testFindBy(): void
    {
        $expectedTitle = 'wanted';
        $entity1 = new TestEntity();
        $entity2 = new TestEntity($expectedTitle);
        $entity3 = new TestEntity($expectedTitle);
        $entity4 = new TestEntity($expectedTitle);
        $entity5 = new TestEntity($expectedTitle);
        $entity6 = new TestEntity();

        self::persistAndFlush($entity1);
        self::persistAndFlush($entity2);
        self::persistAndFlush($entity3);
        self::persistAndFlush($entity4);
        self::persistAndFlush($entity5);
        self::persistAndFlush($entity6);

        $repository = new BaseRepositoryImplementation(self::getEntityManager());
        $entitiesFromDatabase = $repository->findBy(
            criteria: ['title' => $expectedTitle],
            orderBy: ['id' => 'desc'],
            limit: 2,
            offset: 1,
        );

        $this->assertSame([$entity4, $entity3], $entitiesFromDatabase);
    }
}
