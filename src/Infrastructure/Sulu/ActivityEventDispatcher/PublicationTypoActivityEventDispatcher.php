<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEventDispatcher;

use App\Domain\Event\PublicationTypo\CreatedPublicationTypoEvent;
use App\Domain\Event\PublicationTypo\RemovedPublicationTypoEvent;
use App\Domain\Event\PublicationTypo\RestoredPublicationTypoEvent;
use App\Infrastructure\Sulu\ActivityEvent\PublicationTypo\CreatedPublicationTypoActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\PublicationTypo\RemovedPublicationTypoActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\PublicationTypo\RestoredPublicationTypoActivityEvent;

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
