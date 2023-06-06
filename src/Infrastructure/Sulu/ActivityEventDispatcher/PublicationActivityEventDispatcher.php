<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEventDispatcher;

use App\Domain\Event\Publication\CreatedPublicationEvent;
use App\Domain\Event\Publication\DraftRemovedPublicationEvent;
use App\Domain\Event\Publication\ModifiedPublicationEvent;
use App\Domain\Event\Publication\NotifiedPublicationEvent;
use App\Domain\Event\Publication\PublishedPublicationEvent;
use App\Domain\Event\Publication\RemovedPublicationEvent;
use App\Domain\Event\Publication\RestoredPublicationEvent;
use App\Domain\Event\Publication\TranslationCopiedPublicationEvent;
use App\Domain\Event\Publication\UnpublishedPublicationEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\CreatedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\DraftRemovedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\ModifiedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\NotifiedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\PublishedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\RemovedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\RestoredPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\TranslationCopiedPublicationActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Publication\UnpublishedPublicationActivityEvent;

class PublicationActivityEventDispatcher extends AbstractActivityEventDispatcher
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedPublicationEvent::class => 'onCreatedPublication',
            DraftRemovedPublicationEvent::class => 'onDraftRemovedPublication',
            ModifiedPublicationEvent::class => 'onModifiedPublication',
            NotifiedPublicationEvent::class => 'onNotifiedPublication',
            PublishedPublicationEvent::class => 'onPublishedPublication',
            RemovedPublicationEvent::class => 'onRemovedPublication',
            TranslationCopiedPublicationEvent::class => 'onTranslationCopiedPublication',
            RestoredPublicationEvent::class => 'onRestoredPublication',
            UnpublishedPublicationEvent::class => 'onUnpublishedPublication',
        ];
    }

    public function onCreatedPublication(CreatedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new CreatedPublicationActivityEvent($event->getResource()));
    }

    public function onDraftRemovedPublication(DraftRemovedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new DraftRemovedPublicationActivityEvent($event->getResource()));
    }

    public function onModifiedPublication(ModifiedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new ModifiedPublicationActivityEvent($event->getResource()));
    }

    public function onNotifiedPublication(NotifiedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new NotifiedPublicationActivityEvent($event->getResource()));
    }

    public function onPublishedPublication(PublishedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new PublishedPublicationActivityEvent($event->getResource()));
    }

    public function onRemovedPublication(RemovedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RemovedPublicationActivityEvent($event->getResource()));
    }

    public function onTranslationCopiedPublication(TranslationCopiedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new TranslationCopiedPublicationActivityEvent(
            $event->getResource(),
            $event->getSrcLocale(),
            $event->getDestLocale(),
        ));
    }

    public function onRestoredPublication(RestoredPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RestoredPublicationActivityEvent($event->getResource()));
    }

    public function onUnpublishedPublication(UnpublishedPublicationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new UnpublishedPublicationActivityEvent($event->getResource()));
    }
}
