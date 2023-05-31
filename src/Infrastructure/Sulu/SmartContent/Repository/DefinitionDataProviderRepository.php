<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\SmartContent\Repository;

use App\Entity\Definition;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

class DefinitionDataProviderRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait {
        findByFilters as parentFindByFilters;
    }

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param string $alias
     * @param string|null $indexBy
     */
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from(Definition::class, $alias, $indexBy);
    }

    /**
     * @param mixed[] $filters
     */
    public function findByFilters($filters, $page, $pageSize, $limit, $locale, $options = [])
    {
        $definitions = $this->parentFindByFilters($filters, $page, $pageSize, $limit, $locale, $options);

        return \array_map(
            fn (Definition $definition) => $definition->setLocale($locale),
            $definitions,
        );
    }

    /**
     * @param string $alias
     * @param string $locale
     */
    protected function appendJoins(QueryBuilder $queryBuilder, $alias, $locale): void
    {
        $queryBuilder
            ->leftJoin($alias . '.translations', 't')
            ->andWhere('t.locale = :locale')
            ->addSelect('t')
            ->setParameter('locale', $locale);
    }

    /**
     * @param mixed[] $options
     *
     * @return string[]
     */
    protected function append(QueryBuilder $queryBuilder, string $alias, string $locale, $options = []): array
    {
        // $queryBuilder->andWhere($alias . '.published = true');

        return [];
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
        $queryBuilder
            ->innerJoin($alias . '.translations', 'translation', Join::WITH, 'translation.locale = :locale')
            ->setParameter('locale', $locale);
    }
}
