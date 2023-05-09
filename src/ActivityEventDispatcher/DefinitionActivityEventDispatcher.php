<?php

declare(strict_types=1);

namespace App\ActivityEventDispatcher;

use App\ActivityEvent\Definition\CreatedDefinitionActivityEvent;
use App\ActivityEvent\Definition\ModifiedDefinitionActivityEvent;
use App\ActivityEvent\Definition\RemovedDefinitionActivityEvent;
use App\ActivityEvent\Definition\RestoredDefinitionActivityEvent;
use App\DomainEvent\Definition\CreatedDefinitionEvent;
use App\DomainEvent\Definition\ModifiedDefinitionEvent;
use App\DomainEvent\Definition\RemovedDefinitionEvent;
use App\DomainEvent\Definition\RestoredDefinitionEvent;

class DefinitionActivityEventDispatcher extends AbstractActivityEventDispatcher
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedDefinitionEvent::class => 'onCreatedDefinition',
            ModifiedDefinitionEvent::class => 'onModifiedDefinition',
            RemovedDefinitionEvent::class => 'onRemovedDefinition',
            RestoredDefinitionEvent::class => 'onRestoredDefinition',
        ];
    }

    public function onCreatedDefinition(CreatedDefinitionEvent $event): void
    {
        $this->maybeCollectActivityEvent(new CreatedDefinitionActivityEvent($event->getResource()));
    }

    public function onModifiedDefinition(ModifiedDefinitionEvent $event): void
    {
        $this->maybeCollectActivityEvent(new ModifiedDefinitionActivityEvent($event->getResource()));
    }

    public function onRemovedDefinition(RemovedDefinitionEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RemovedDefinitionActivityEvent($event->getResource()));
    }

    public function onRestoredDefinition(RestoredDefinitionEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RestoredDefinitionActivityEvent($event->getResource()));
    }
}
