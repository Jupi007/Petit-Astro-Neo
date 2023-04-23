<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Definition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Definition::class);
    }

    public function save(Definition $definition, bool $flush = false): void
    {
        $this->getEntityManager()->persist($definition);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Definition $definition, bool $flush = false): void
    {
        $this->getEntityManager()->remove($definition);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
}
