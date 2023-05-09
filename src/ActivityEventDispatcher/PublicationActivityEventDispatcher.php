<?php

declare(strict_types=1);

namespace App\ActivityEventDispatcher;

use App\ActivityEvent\Publication\CreatedPublicationActivityEvent;
use App\ActivityEvent\Publication\DraftRemovedPublicationActivityEvent;
use App\ActivityEvent\Publication\ModifiedPublicationActivityEvent;
use App\ActivityEvent\Publication\NotifiedPublicationActivityEvent;
use App\ActivityEvent\Publication\PublishedPublicationActivityEvent;
use App\ActivityEvent\Publication\RemovedPublicationActivityEvent;
use App\ActivityEvent\Publication\RestoredPublicationActivityEvent;
use App\ActivityEvent\Publication\TranslationCopiedPublicationActivityEvent;
use App\ActivityEvent\Publication\UnpublishedPublicationActivityEvent;
use App\DomainEvent\Publication\CreatedPublicationEvent;
use App\DomainEvent\Publication\DraftRemovedPublicationEvent;
use App\DomainEvent\Publication\ModifiedPublicationEvent;
use App\DomainEvent\Publication\NotifiedPublicationEvent;
use App\DomainEvent\Publication\PublishedPublicationEvent;
use App\DomainEvent\Publication\RemovedPublicationEvent;
use App\DomainEvent\Publication\RestoredPublicationEvent;
use App\DomainEvent\Publication\TranslationCopiedPublicationEvent;
use App\DomainEvent\Publication\UnpublishedPublicationEvent;

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
