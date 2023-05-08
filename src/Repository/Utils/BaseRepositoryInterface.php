<?php

declare(strict_types=1);

namespace App\Repository\Utils;

/** @template T of object */
interface BaseRepositoryInterface
{
    /** @param T $entity */
    public function save(object $entity): void;

    /** @param T $entity */
    public function remove(object $entity): void;

    /** @return T|null */
    public function findOne(mixed $id): ?object;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T|null
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?object;

    /** @return T[] */
    public function findAll(): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T[]
     */
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array;
}
