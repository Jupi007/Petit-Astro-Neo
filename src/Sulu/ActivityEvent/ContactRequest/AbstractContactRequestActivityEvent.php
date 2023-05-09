<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\ContactRequest;

use App\Entity\ContactRequest;
use App\Sulu\ActivityEvent\AbstractActivityEvent;
use App\Sulu\Admin\ContactRequestAdmin;

abstract class AbstractContactRequestActivityEvent extends AbstractActivityEvent
{
    public function __construct(
        private readonly ContactRequest $contactRequest,
    ) {
        parent::__construct();
    }

    public function getResourceKey(): string
    {
        return ContactRequest::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->contactRequest->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->contactRequest->getEmail();
    }

    public function getResourceSecurityContext(): ?string
    {
        return ContactRequestAdmin::SECURITY_CONTEXT;
    }
}
