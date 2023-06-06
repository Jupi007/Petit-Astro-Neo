<?php

declare(strict_types=1);

namespace App\Domain\Event\PublicationTypo;

use App\Domain\Entity\PublicationTypo;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractPublicationTypoEvent extends Event
{
    public function __construct(
        private readonly PublicationTypo $resource,
    ) {
    }

    public function getResource(): PublicationTypo
    {
        return $this->resource;
    }
}
