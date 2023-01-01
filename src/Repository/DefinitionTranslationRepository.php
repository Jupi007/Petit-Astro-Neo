<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DefinitionTranslation;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionTranslation::class);
    }
}
