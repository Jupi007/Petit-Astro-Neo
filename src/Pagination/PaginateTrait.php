<?php

declare(strict_types=1);

namespace App\Pagination;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @template-covariant T
 */
trait PaginateTrait
{
    /** @return Paginator<T> */
    private function paginate(Query|QueryBuilder $query, int $page, int $limit): Paginator
    {
        if ($page < 1) {
            throw new NotFoundHttpException('The page ' . $page . ' does not exist');
        } elseif ($limit < 1) {
            throw new NotFoundHttpException('$limit should be greater than 0');
        }

        /** @var Paginator<T> */
        $paginator = new Paginator($query, $page, $limit);

        if ($page > $paginator->getNbPages() && 0 !== $paginator->getNbPages()) {
            throw new NotFoundHttpException('The page ' . $page . ' does not exist');
        }

        return $paginator;
    }
}
