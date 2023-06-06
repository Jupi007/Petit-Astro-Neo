<?php

declare(strict_types=1);

namespace App\Domain\Event\Publication;

use App\Domain\Entity\Publication;

class TranslationCopiedPublicationEvent extends AbstractPublicationEvent
{
    public function __construct(
        Publication $resource,
        private readonly string $srcLocale,
        private readonly string $destLocale,
    ) {
        parent::__construct($resource);
    }

    public function getSrcLocale(): string
    {
        return $this->srcLocale;
    }

    public function getDestLocale(): string
    {
        return $this->destLocale;
    }
}
