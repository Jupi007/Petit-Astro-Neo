<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEvent\NewsletterRegistration;

use App\Entity\NewsletterRegistration;
use App\Sulu\ActivityEvent\AbstractActivityEvent;
use App\Sulu\Admin\NewsletterRegistrationAdmin;

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
