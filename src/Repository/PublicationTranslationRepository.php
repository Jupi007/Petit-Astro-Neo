<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicationTranslation;
use App\Repository\Pagination\PaginateTrait;
use App\Repository\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicationTranslation>
 *
 * @method PublicationTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationTranslation[] findAll()
 * @method PublicationTranslation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationTranslationRepository extends ServiceEntityRepository
{
    /** @template-use PaginateTrait<PublicationTranslation> */
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationTranslation::class);
    }

    /** @return Paginator<PublicationTranslation> */
    public function findPaginatedForSitemap(int $page, int $limit): Paginator
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('t')
            ->innerJoin('t.publication', 'p')
            ->addSelect('p')
            ->innerJoin('p.translations', 'altT')
            ->addSelect('altT');

        return $this->paginate($qb, $page, $limit);
    }
}
