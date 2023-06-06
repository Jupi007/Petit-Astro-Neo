<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEventDispatcher;

use App\Domain\Event\Definition\CreatedDefinitionEvent;
use App\Domain\Event\Definition\ModifiedDefinitionEvent;
use App\Domain\Event\Definition\RemovedDefinitionEvent;
use App\Domain\Event\Definition\RestoredDefinitionEvent;
use App\Domain\Event\Definition\TranslationCopiedDefinitionEvent;
use App\Infrastructure\Sulu\ActivityEvent\Definition\CreatedDefinitionActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Definition\ModifiedDefinitionActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Definition\RemovedDefinitionActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Definition\RestoredDefinitionActivityEvent;
use App\Infrastructure\Sulu\ActivityEvent\Definition\TranslationCopiedDefinitionActivityEvent;

class DefinitionActivityEventDispatcher extends AbstractActivityEventDispatcher
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreatedDefinitionEvent::class => 'onCreatedDefinition',
            ModifiedDefinitionEvent::class => 'onModifiedDefinition',
            TranslationCopiedDefinitionEvent::class => 'onTranslationCopiedDefinition',
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

    public function onTranslationCopiedDefinition(TranslationCopiedDefinitionEvent $event): void
    {
        $this->maybeCollectActivityEvent(new TranslationCopiedDefinitionActivityEvent(
            $event->getResource(),
            $event->getSrcLocale(),
            $event->getDestLocale(),
        ));
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
