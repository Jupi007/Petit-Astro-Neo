<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Entity\Publication;
use App\Infrastructure\Sulu\ActivityEvent\Trait\TranslationCopiedActivityEventTrait;

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
