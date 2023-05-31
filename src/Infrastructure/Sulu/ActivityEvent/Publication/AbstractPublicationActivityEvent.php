<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\Publication;

use App\Entity\Publication;
use App\Infrastructure\Sulu\ActivityEvent\AbstractActivityEvent;
use App\Infrastructure\Sulu\Admin\PublicationAdmin;

abstract class AbstractPublicationActivityEvent extends AbstractActivityEvent
{
    public function __construct(
        private readonly Publication $publication,
    ) {
        parent::__construct();
    }

    public function getResourceKey(): string
    {
        return Publication::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->publication->getId();
    }

    public function getResourceTitle(): ?string
    {
        return '';
        // return $this->publication->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }
}
