<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Common;

use App\Entity\Contract\LocalizableEntityInterface;

/** @template T of LocalizableEntityInterface */
trait FindLocalizedRepositoryTrait
{
    abstract public function findOne(mixed $id): ?object;

    /** @return T|null */
    public function findOneLocalized(mixed $id, string $locale): ?LocalizableEntityInterface
    {
        return $this->setEntityLocale(
            object: $this->findOne($id),
            locale: $locale,
        );
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    abstract public function findOneBy(array $criteria, array $orderBy = null): ?object;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T|null
     */
    public function findOneLocalizedBy(array $criteria, string $locale, array $orderBy = null): ?LocalizableEntityInterface
    {
        return $this->setEntityLocale(
            object: $this->findOneBy($criteria, $orderBy),
            locale: $locale,
        );
    }

    /** @return object[] */
    abstract public function findAll(): array;

    /** @return T[] */
    public function findAllLocalized(string $locale): array
    {
        return $this->setEntitiesLocale(
            objects: $this->findAll(),
            locale: $locale,
        );
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return object[]
     */
    abstract public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T[]
     */
    public function findLocalizedBy(array $criteria, string $locale, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        return $this->setEntitiesLocale(
            objects: $this->findBy($criteria, $orderBy, $limit, $offset),
            locale: $locale,
        );
    }

    /**
     * @param T|null $object
     *
     * @return T|null
     */
    private function setEntityLocale(?LocalizableEntityInterface $object, string $locale): ?LocalizableEntityInterface
    {
        $object?->setLocale($locale);

        return $object;
    }

    /**
     * @param T[] $objects
     *
     * @return T[]
     */
    private function setEntitiesLocale(array $objects, string $locale): array
    {
        foreach ($objects as &$object) {
            $this->setEntityLocale($object, $locale);
        }

        return $objects;
    }
}
