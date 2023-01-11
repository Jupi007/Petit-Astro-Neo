<?php

declare(strict_types=1);

namespace App\Pagination;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @template-covariant T
 *
 * @extends DoctrinePaginator<T>
 */
class Paginator extends DoctrinePaginator
{
    public function __construct(
        Query|QueryBuilder $query,
        private readonly int $page,
        private readonly int $limit,
        bool $fetchJoinCollection = true,
    ) {
        parent::__construct($query, $fetchJoinCollection);

        $this->getQuery()
             ->setFirstResult($limit * ($page - 1))
             ->setMaxResults($limit);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getNbPages(): int
    {
        return (int) \ceil($this->count() / $this->limit);
    }
}
