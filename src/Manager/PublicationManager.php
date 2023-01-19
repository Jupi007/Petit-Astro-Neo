<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Publication;
use App\Event\Publication\CreatedPublicationActivityEvent;
use App\Event\Publication\ModifiedPublicationActivityEvent;
use App\Event\Publication\RemovedPublicationActivityEvent;
use App\Repository\PublicationRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PublicationManager
{
    public function __construct(
        private readonly PublicationRepository $repository,
        private readonly DomainEventCollectorInterface $domainEventCollector,
        private readonly TrashManagerInterface $trashManager,
        private readonly RouteManagerInterface $routeManager,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function createFromRequest(Request $request): Publication
    {
        $publication = new Publication();

        $this->mapRequestToPublication($publication, $request);
        $this->domainEventCollector->collect(new CreatedPublicationActivityEvent($publication));
        $this->repository->save($publication);

        $this->generatePublicationRoute($publication, $request);
        $this->repository->save($publication);

        return $publication;
    }

    public function updateFromRequest(Publication $publication, Request $request): Publication
    {
        $this->mapRequestToPublication($publication, $request);
        $this->generatePublicationRoute($publication, $request);
        $this->domainEventCollector->collect(new ModifiedPublicationActivityEvent($publication));
        $this->repository->save($publication);

        return $publication;
    }

    public function remove(Publication $publication): void
    {
        $this->trashManager->store(Publication::RESOURCE_KEY, $publication);
        $this->domainEventCollector->collect(new RemovedPublicationActivityEvent($publication));
        $this->removeRoutes($publication);
        $this->repository->remove($publication);
    }

    private function mapRequestToPublication(Publication $publication, Request $request): void
    {
        $data = $request->toArray();
        $locale = $request->query->get('locale');

        $publication
            ->setLocale($locale ?? '')
            ->setTitle($data['title'] ?? '')
            ->setSubtitle($data['subtitle'] ?? '')
            ->setContent($data['content'] ?? []);
    }

    private function generatePublicationRoute(Publication $publication, Request $request): void
    {
        $route = (string) $request->toArray()['routePath'];

        if (null === $publication->getRoute()) {
            $this->routeManager->create($publication, $route);
        } elseif ($publication->getRoute()->getPath() !== $route) {
            $this->routeManager->update($publication, $route);
        }
    }

    private function removeRoutes(Publication $publication): void
    {
        foreach ($publication->getLocales() as $locale) {
            $publication->setLocale($locale);

            if (null !== $route = $publication->getRoute()) {
                $this->routeRepository->remove($route);

                foreach ($route->getHistories() as $historyRoute) {
                    $this->routeRepository->remove($historyRoute);
                }
            }
        }
    }
}
