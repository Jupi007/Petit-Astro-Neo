<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\ContactRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;

/**
 * @extends ServiceEntityRepository<ContactRequest>
 *
 * @method ContactRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRequest[] findAll()
 * @method ContactRequest[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRequestRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
        parent::__construct($registry, ContactRequest::class);
    }

    public function createDoctrineListRepresentation(): PaginatedRepresentation
    {
        return $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            ContactRequest::RESOURCE_KEY,
        );
    }

    public function save(ContactRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
