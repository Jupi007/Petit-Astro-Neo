<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEvent\NewsletterRegistration;

use App\Domain\Entity\NewsletterRegistration;
use App\Infrastructure\Sulu\ActivityEvent\AbstractActivityEvent;
use App\Infrastructure\Sulu\Admin\NewsletterRegistrationAdmin;

abstract class AbstractNewsletterRegistrationActivityEvent extends AbstractActivityEvent
{
    public function __construct(
        private readonly NewsletterRegistration $definition,
    ) {
        parent::__construct();
    }

    public function getResourceKey(): string
    {
        return NewsletterRegistration::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->definition->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->definition->getEmail();
    }

    public function getResourceSecurityContext(): ?string
    {
        return NewsletterRegistrationAdmin::SECURITY_CONTEXT;
    }
}
