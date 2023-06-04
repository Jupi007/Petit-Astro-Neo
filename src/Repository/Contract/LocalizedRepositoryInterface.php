<?php

declare(strict_types=1);

namespace App\Repository\Contract;

use App\Entity\Contract\LocalizableEntityInterface;

/** @template T of LocalizableEntityInterface */
interface LocalizedRepositoryInterface
{
    /** @return T|null */
    public function findOneLocalized(mixed $id, string $locale): ?LocalizableEntityInterface;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T|null
     */
    public function findOneLocalizedBy(array $criteria, string $locale, array $orderBy = null): ?LocalizableEntityInterface;

    /** @return T */
    public function getOneLocalized(mixed $id, string $locale): LocalizableEntityInterface;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T
     */
    public function getOneLocalizedBy(array $criteria, string $locale, array $orderBy = null): LocalizableEntityInterface;

    /** @return T[] */
    public function findAllLocalized(string $locale): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T[]
     */
    public function findLocalizedBy(array $criteria, string $locale, array $orderBy = null, int $limit = null, int $offset = null): array;
}
