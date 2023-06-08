<?php

declare(strict_types=1);

namespace App\Event\PublicationTypo;

use App\Entity\PublicationTypo;
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
