<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\ActivityEventDispatcher;

use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractActivityEventDispatcher implements EventSubscriberInterface
{
    public function __construct(
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly string $suluContext,
    ) {
    }

    protected function maybeCollectActivityEvent(DomainEvent $domainEvent): void
    {
        if ('admin' === $this->suluContext) {
            $this->domainEventCollector->collect($domainEvent);
        }
    }
}
