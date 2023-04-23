<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicationTypo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicationTypo>
 *
 * @method PublicationTypo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationTypo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationTypo[] findAll()
 * @method PublicationTypo[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationTypoRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, PublicationTypo::class);
    }

    public function save(PublicationTypo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PublicationTypo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
