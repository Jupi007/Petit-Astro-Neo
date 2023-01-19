<?php

declare(strict_types=1);

namespace App\Event\Publication;

use App\Admin\PublicationAdmin;
use App\Entity\Publication;
use App\Event\AbstractActivityEvent;

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

    /**
     * @return array<string, ?string>
     */
    public function getEventPayload(): array
    {
        return ['name' => $this->publication->getTitle()];
    }

    public function getResourceTitle(): ?string
    {
        return $this->publication->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }
}
