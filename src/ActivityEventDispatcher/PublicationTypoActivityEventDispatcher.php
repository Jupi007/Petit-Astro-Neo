<?php

declare(strict_types=1);

namespace App\ActivityEventDispatcher;

use App\ActivityEvent\PublicationTypo\CreatedPublicationTypoActivityEvent;
use App\ActivityEvent\PublicationTypo\RemovedPublicationTypoActivityEvent;
use App\ActivityEvent\PublicationTypo\RestoredPublicationTypoActivityEvent;
use App\DomainEvent\PublicationTypo\CreatedPublicationTypoEvent;
use App\DomainEvent\PublicationTypo\RemovedPublicationTypoEvent;
use App\DomainEvent\PublicationTypo\RestoredPublicationTypoEvent;

class PublicationTypoActivityEventDispatcher extends AbstractActivityEventDispatcher
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedPublicationTypoEvent::class => 'onCreatedPublicationTypo',
            RemovedPublicationTypoEvent::class => 'onRemovedPublicationTypo',
            RestoredPublicationTypoEvent::class => 'onRestoredPublicationTypo',
        ];
    }

    public function onCreatedPublicationTypo(CreatedPublicationTypoEvent $event): void
    {
        $this->maybeCollectActivityEvent(new CreatedPublicationTypoActivityEvent($event->getResource()));
    }

    public function onRemovedPublicationTypo(RemovedPublicationTypoEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RemovedPublicationTypoActivityEvent($event->getResource()));
    }

    public function onRestoredPublicationTypo(RestoredPublicationTypoEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RestoredPublicationTypoActivityEvent($event->getResource()));
    }
}
