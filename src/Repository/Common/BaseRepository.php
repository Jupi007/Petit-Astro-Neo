<?php

declare(strict_types=1);

namespace App\Repository\Common;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/** @template T of object */
abstract class BaseRepository
{
    /** @var EntityRepository<T> */
    private readonly EntityRepository $repository;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
    ) {
        $this->repository = $entityManager->getRepository(static::getClassName());
    }

    /** @return class-string<T> */
    abstract protected static function getClassName(): string;

    /** @param T $entity */
    public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /** @param T $entity */
    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /** @return T|null */
    public function findOne(mixed $id): ?object
    {
        return $this->repository->find($id);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T|null
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?object
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /** @return T[] */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T[]
     */
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }
}
