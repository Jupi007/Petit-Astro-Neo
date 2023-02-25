<?php

declare(strict_types=1);

namespace App\Event\PublicationTypo;

use App\Admin\PublicationTypoAdmin;
use App\Entity\PublicationTypo;
use App\Event\AbstractActivityEvent;

abstract class AbstractPublicationTypoActivityEvent extends AbstractActivityEvent
{
    public function __construct(
        private readonly PublicationTypo $typo,
    ) {
        parent::__construct();
    }

    public function getResourceKey(): string
    {
        return PublicationTypo::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->typo->getId();
    }

    public function getResourceSecurityContext(): ?string
    {
        return PublicationTypoAdmin::SECURITY_CONTEXT;
    }
}
