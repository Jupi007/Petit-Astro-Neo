<?php

declare(strict_types=1);

namespace App\ActivityEvent;

use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

abstract class AbstractActivityEvent extends DomainEvent
{
    abstract protected function getActivityEventType(): ActivityEventType;

    public function getEventType(): string
    {
        return $this->getActivityEventType()->value;
    }
}
