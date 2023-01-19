<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DefinitionTranslation;
use App\Repository\Pagination\PaginateTrait;
use App\Repository\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionTranslation>
 *
 * @method DefinitionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefinitionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefinitionTranslation[] findAll()
 * @method DefinitionTranslation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionTranslationRepository extends ServiceEntityRepository
{
    /** @template-use PaginateTrait<DefinitionTranslation> */
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionTranslation::class);
    }

    /** @return Paginator<DefinitionTranslation> */
    public function findPaginatedForSitemap(int $page, int $limit): Paginator
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('t')
            ->innerJoin('t.definition', 'd')
            ->addSelect('d')
            ->innerJoin('d.translations', 'altT')
            ->addSelect('altT');

        return $this->paginate($qb, $page, $limit);
    }
}
