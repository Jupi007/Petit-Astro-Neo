<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\PublicationTypo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;

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
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
        parent::__construct($registry, PublicationTypo::class);
    }

    public function save(PublicationTypo $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(PublicationTypo $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function createDoctrineListRepresentation(?string $locale, ?string $publicationId): PaginatedRepresentation
    {
        return $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            PublicationTypo::RESOURCE_KEY,
            filters: null !== $publicationId ? ['publicationId' => $publicationId] : [],
            parameters: ['locale' => $locale],
        );
    }
}
