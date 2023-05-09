<?php

declare(strict_types=1);

namespace App\ActivityEventDispatcher;

use App\ActivityEvent\ContactRequest\CreatedContactRequestActivityEvent;
use App\ActivityEvent\ContactRequest\ModifiedContactRequestActivityEvent;
use App\ActivityEvent\ContactRequest\RemovedContactRequestActivityEvent;
use App\ActivityEvent\ContactRequest\RestoredContactRequestActivityEvent;
use App\DomainEvent\ContactRequest\CreatedContactRequestEvent;
use App\DomainEvent\ContactRequest\ModifiedContactRequestEvent;
use App\DomainEvent\ContactRequest\RemovedContactRequestEvent;
use App\DomainEvent\ContactRequest\RestoredContactRequestEvent;

class ContactRequestActivityEventDispatcher extends AbstractActivityEventDispatcher
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedContactRequestEvent::class => 'onCreatedContactRequest',
            ModifiedContactRequestEvent::class => 'onModifiedContactRequest',
            RemovedContactRequestEvent::class => 'onRemovedContactRequest',
            RestoredContactRequestEvent::class => 'onRestoredContactRequest',
        ];
    }

    public function onCreatedContactRequest(CreatedContactRequestEvent $event): void
    {
        $this->maybeCollectActivityEvent(new CreatedContactRequestActivityEvent($event->getResource()));
    }

    public function onModifiedContactRequest(ModifiedContactRequestEvent $event): void
    {
        $this->maybeCollectActivityEvent(new ModifiedContactRequestActivityEvent($event->getResource()));
    }

    public function onRemovedContactRequest(RemovedContactRequestEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RemovedContactRequestActivityEvent($event->getResource()));
    }

    public function onRestoredContactRequest(RestoredContactRequestEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RestoredContactRequestActivityEvent($event->getResource()));
    }
}
