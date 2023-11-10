<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Entity\Definition;
use App\Event\Definition\CreatedDefinitionEvent;
use App\Event\Definition\ModifiedDefinitionEvent;
use App\Event\Definition\RemovedDefinitionEvent;
use App\Event\Definition\TranslationCopiedDefinitionEvent;
use App\Manager\Data\Definition\CreateDefinitionData;
use App\Manager\Data\Definition\UpdateDefinitionData;
use App\Manager\DefinitionManager;
use App\Repository\DefinitionRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefinitionManagerTest extends TestCase
{
    public function testCreate(): void
    {
        $locale = 'fr';
        $title = 'title';
        $description = 'description';
        $routePath = '/routePath';
        $data = new CreateDefinitionData(
            title: $title,
            description: $description,
            routePath: $routePath,
            locale: $locale,
        );

        $repository = $this->createMock(DefinitionRepositoryInterface::class);
        $repository
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->isInstanceOf(Definition::class));
        $eventDispatcher = $this->createEventDispatcherMock(
            CreatedDefinitionEvent::class,
        );

        $manager = new DefinitionManager($repository, $eventDispatcher);
        $definition = $manager->create($data);

        $this->assertSame($locale, $definition->getLocale());
        $this->assertSame($title, $definition->getTitle());
        $this->assertSame($description, $definition->getDescription());
        $this->assertSame($routePath, $definition->getRoutePath());
    }

    public function testUpdate(): void
    {
        $id = 123;
        $locale = 'fr';
        $newTitle = 'new title';
        $newDescription = 'new description';
        $newRoutePath = '/routePath/new';
        $data = new UpdateDefinitionData(
            id: $id,
            title: $newTitle,
            description: $newDescription,
            routePath: $newRoutePath,
            locale: $locale,
        );

        $definition = new Definition();
        $definition->setLocale($locale);
        $definition->getTranslation();

        $repository = $this->createMock(DefinitionRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOneLocalized')
            ->with($id, $locale)
            ->willReturn($definition);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Definition::class));
        $eventDispatcher = $this->createEventDispatcherMock(
            ModifiedDefinitionEvent::class,
        );

        $manager = new DefinitionManager($repository, $eventDispatcher);
        $updatedDefinition = $manager->update($data);

        $this->assertSame($locale, $updatedDefinition->getLocale());
        $this->assertSame($newTitle, $updatedDefinition->getTitle());
        $this->assertSame($newDescription, $updatedDefinition->getDescription());
        $this->assertSame($newRoutePath, $updatedDefinition->getRoutePath());
    }

    public function testCopyLocale(): void
    {
        $id = 123;
        $srcLocale = 'fr';
        $destLocale = 'fr';
        $title = 'title';
        $description = 'description';
        $routePath = '/routePath';

        $definition = new Definition();
        $definition
            ->setLocale($srcLocale)
            ->setTitle($title)
            ->setDescription($description)
            ->setRoutePath($routePath);

        $repository = $this->createMock(DefinitionRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOneLocalized')
            ->with($id, $srcLocale)
            ->willReturn($definition);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Definition::class));
        $eventDispatcher = $this->createEventDispatcherMock(
            TranslationCopiedDefinitionEvent::class,
        );

        $manager = new DefinitionManager($repository, $eventDispatcher);
        $newDefinition = $manager->copyLocale($id, $srcLocale, $destLocale);

        $this->assertSame($destLocale, $newDefinition->getLocale());
        $this->assertSame($title, $definition->getTitle());
        $this->assertSame($description, $definition->getDescription());
        $this->assertSame($routePath, $definition->getRoutePath());
    }

    public function testRemove(): void
    {
        $id = 123;
        $definition = $this->createMock(Definition::class);

        $repository = $this->createMock(DefinitionRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOne')
            ->with($id)
            ->willReturn($definition);
        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($definition);
        $eventDispatcher = $this->createEventDispatcherMock(
            RemovedDefinitionEvent::class,
        );

        $manager = new DefinitionManager($repository, $eventDispatcher);
        $manager->remove($id);
    }

    /** @param class-string $eventType */
    private function createEventDispatcherMock(string $eventType): EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf($eventType));

        return $eventDispatcher;
    }
}
