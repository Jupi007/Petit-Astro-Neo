<?php

declare(strict_types=1);

namespace App\Sulu\ActivityEventDispatcher;

use App\Event\NewsletterRegistration\CreatedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\ModifiedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\RemovedNewsletterRegistrationEvent;
use App\Event\NewsletterRegistration\RestoredNewsletterRegistrationEvent;
use App\Sulu\ActivityEvent\NewsletterRegistration\CreatedNewsletterRegistrationActivityEvent;
use App\Sulu\ActivityEvent\NewsletterRegistration\ModifiedNewsletterRegistrationActivityEvent;
use App\Sulu\ActivityEvent\NewsletterRegistration\RemovedNewsletterRegistrationActivityEvent;
use App\Sulu\ActivityEvent\NewsletterRegistration\RestoredNewsletterRegistrationActivityEvent;

class NewsletterRegistrationActivityEventDispatcher extends AbstractActivityEventDispatcher
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedNewsletterRegistrationEvent::class => 'onCreatedNewsletterRegistration',
            ModifiedNewsletterRegistrationEvent::class => 'onModifiedNewsletterRegistration',
            RemovedNewsletterRegistrationEvent::class => 'onRemovedNewsletterRegistration',
            RestoredNewsletterRegistrationEvent::class => 'onRestoredNewsletterRegistration',
        ];
    }

    public function onCreatedNewsletterRegistration(CreatedNewsletterRegistrationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new CreatedNewsletterRegistrationActivityEvent($event->getResource()));
    }

    public function onModifiedNewsletterRegistration(ModifiedNewsletterRegistrationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new ModifiedNewsletterRegistrationActivityEvent($event->getResource()));
    }

    public function onRemovedNewsletterRegistration(RemovedNewsletterRegistrationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RemovedNewsletterRegistrationActivityEvent($event->getResource()));
    }

    public function onRestoredNewsletterRegistration(RestoredNewsletterRegistrationEvent $event): void
    {
        $this->maybeCollectActivityEvent(new RestoredNewsletterRegistrationActivityEvent($event->getResource()));
    }
}
