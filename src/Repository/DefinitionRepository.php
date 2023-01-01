<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Definition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

/**
 * @extends ServiceEntityRepository<Definition>
 *
 * @method Definition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Definition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Definition[] findAll()
 * @method Definition[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionRepository extends ServiceEntityRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait {
        findByFilters as parentFindByFilters;
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Definition::class);
    }

    public function create(string $locale): Definition
    {
        $definition = new Definition();
        $definition->setLocale($locale);

        return $definition;
    }

    public function remove(Definition $definition): void
    {
        $this->getEntityManager()->remove($definition);
        $this->getEntityManager()->flush();
    }

    public function save(Definition $definition): void
    {
        $this->getEntityManager()->persist($definition);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id, string $locale): ?Definition
    {
        $definition = $this->find($id);
        if (!$definition instanceof Definition) {
            return null;
        }

        $definition->setLocale($locale);

        return $definition;
    }

    /**
     * @param mixed[] $filters
     */
    public function findByFilters($filters, $page, $pageSize, $limit, $locale, $options = [])
    {
        $entities = $this->parentFindByFilters($filters, $page, $pageSize, $limit, $locale, $options);

        return \array_map(
            fn (Definition $entity) => $entity->setLocale($locale),
            $entities,
        );
    }

    /**
     * @param string $alias
     * @param string $locale
     *
     * @return void
     */
    protected function appendJoins(QueryBuilder $queryBuilder, $alias, $locale)
    {
        // join and select entities that are used for creating data items or resource items in the DataProvider here
    }

    /**
     * @param mixed[] $options
     *
     * @return string[]
     */
    protected function append(QueryBuilder $queryBuilder, string $alias, string $locale, $options = []): array
    {
        $queryBuilder->andWhere($alias . '.enabled = true');

        return [];
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
        $queryBuilder->innerJoin($alias . '.translations', 'translation', Join::WITH, 'translation.locale = :locale');
        $queryBuilder->setParameter('locale', $locale);
    }
}
