<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Trash;

use App\Domain\Entity\Contract\TrashableEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;

#[AsDoctrineListener(event: Events::preRemove)]
class TrashableEntityEventSubscriber
{
    public function __construct(
        private readonly TrashManagerInterface $trashManager,
    ) {
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof TrashableEntityInterface) {
            return;
        }

        $this->trashManager->store(
            $object->getResourceKey(),
            $object,
        );
    }
}
