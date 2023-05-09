<?php

declare(strict_types=1);

namespace App\Repository\Common;

use App\Entity\Contract\LocalizableEntityInterface;

/** @template T of LocalizableEntityInterface */
interface FindLocalizedRepositoryInterface
{
    /** @return T|null */
    public function findOneLocalized(mixed $id, string $locale): ?LocalizableEntityInterface;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T|null
     */
    public function findOneLocalizedBy(array $criteria, array $orderBy = null, string $locale): ?LocalizableEntityInterface;

    /** @return T[] */
    public function findAllLocalized(string $locale): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T[]
     */
    public function findLocalizedBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null, string $locale): array;
}
