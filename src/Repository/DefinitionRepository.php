<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Definition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

/**
 * @extends ServiceEntityRepository<Definition>
 *
 * @method Definition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Definition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Definition[] findAll()
 * @method Definition[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionRepository extends ServiceEntityRepository
{
    use DataProviderRepositoryTrait {
        findByFilters as parentFindByFilters;
    }

    public function __construct(
        ManagerRegistry $registry,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
        parent::__construct($registry, Definition::class);
    }

    public function save(Definition $definition): void
    {
        $this->getEntityManager()->persist($definition);
        $this->getEntityManager()->flush();
    }

    public function remove(Definition $definition): void
    {
        $this->getEntityManager()->remove($definition);
        $this->getEntityManager()->flush();
    }

    public function createDoctrineListRepresentation(string $locale): PaginatedRepresentation
    {
        return $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Definition::RESOURCE_KEY,
            parameters: ['locale' => $locale],
        );
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
     * @param string $alias
     * @param string $locale
     *
     * @return void
     */
    protected function appendJoins(QueryBuilder $queryBuilder, $alias, $locale)
    {
        // join and select entities that are used for creating data items or resource items in the DataProvider here
    }
}
