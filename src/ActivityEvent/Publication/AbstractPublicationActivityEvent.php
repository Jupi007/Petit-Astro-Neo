<?php

declare(strict_types=1);

namespace App\ActivityEvent\Publication;

use App\ActivityEvent\AbstractActivityEvent;
use App\Admin\PublicationAdmin;
use App\Entity\Publication;

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
