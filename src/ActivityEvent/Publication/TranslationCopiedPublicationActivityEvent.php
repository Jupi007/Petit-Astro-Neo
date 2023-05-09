<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\Trait\TranslationCopiedActivityEventTrait;
use App\Entity\Publication;

class TranslationCopiedPublicationActivityEvent extends AbstractPublicationActivityEvent
{
    use TranslationCopiedActivityEventTrait;

    public function __construct(
        Publication $publication,
        private readonly string $srcLocale,
        private readonly string $destLocale,
    ) {
        parent::__construct($publication);
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
