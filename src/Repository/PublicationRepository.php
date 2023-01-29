<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;

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
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
        parent::__construct($registry, Publication::class);
    }

    public function save(Publication $definition): void
    {
        $this->getEntityManager()->persist($definition);
        $this->getEntityManager()->flush();
    }

    public function remove(Publication $definition): void
    {
        $this->getEntityManager()->remove($definition);
        $this->getEntityManager()->flush();
    }

    public function createDoctrineListRepresentation(?string $locale): PaginatedRepresentation
    {
        return $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Publication::RESOURCE_KEY,
            parameters: ['locale' => $locale],
            includedFields: ['locale', 'ghostLocale'],
        );
    }
}
