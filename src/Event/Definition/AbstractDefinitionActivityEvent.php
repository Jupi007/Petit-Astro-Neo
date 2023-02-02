<?php

declare(strict_types=1);

namespace App\Event\Definition;

use App\Admin\DefinitionAdmin;
use App\Entity\Definition;
use App\Event\AbstractActivityEvent;

abstract class AbstractDefinitionActivityEvent extends AbstractActivityEvent
{
    public function __construct(
        private readonly Definition $definition,
    ) {
        parent::__construct();
    }

    public function getResourceKey(): string
    {
        return Definition::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->definition->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->definition->getTitle();
    }

    public function getResourceTitleLocale(): string
    {
        return $this->definition->getLocale();
    }

    public function getResourceSecurityContext(): ?string
    {
        return DefinitionAdmin::SECURITY_CONTEXT;
    }
}
