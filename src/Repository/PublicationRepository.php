<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 *
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[] findAll()
 * @method Publication[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Publication::class);
    }

    public function save(Publication $definition, bool $flush = false): void
    {
        $this->getEntityManager()->persist($definition);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Publication $definition, bool $flush = false): void
    {
        $this->getEntityManager()->remove($definition);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
