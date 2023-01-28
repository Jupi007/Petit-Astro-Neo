<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Entity\Publication;
use App\Manager\PublicationManager;
use App\Repository\PublicationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/admin/api/publications')]
class PublicationController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage,
        private readonly PublicationRepository $publicationRepository,
        private readonly PublicationManager $publicationManager,
    ) {
        parent::__construct($viewHandler, $tokenStorage);
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Rest\Get(name: 'app.admin.get_publication_list')]
    public function cget(Request $request): View
    {
        $listRepresentation = $this->publicationRepository->createDoctrineListRepresentation(
            $this->getLocale($request),
        );

        return View::create($listRepresentation->toArray());
    }

    #[Rest\Get(path: '/{id}', name: 'app.admin.get_publication')]
    public function get(Publication $publication, Request $request): View
    {
        $dimensionAttributes = $this->getDimensionAttributes($request);

        return View::create($this->normalize($publication, $dimensionAttributes));
    }

    #[Rest\Post(name: 'app.admin.post_publication')]
    public function post(Request $request): View
    {
        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request);

        $publication = $this->publicationManager->create($data, $dimensionAttributes);

        if ('publish' === $this->getAction($request)) {
            $this->publicationManager->publish($publication, $dimensionAttributes);
        }

        return View::create(
            $this->normalize($publication, $dimensionAttributes),
            Response::HTTP_CREATED,
        );
    }

    #[Rest\Post(path: '/{id}', name: 'app.admin.post_trigger_publication')]
    public function postTrigger(Publication $publication, Request $request): View
    {
        $dimensionAttributes = $this->getDimensionAttributes($request);
        $action = $this->getAction($request);

        switch ($action) {
            case 'copy-locale':
                $this->publicationManager->copyLocale(
                    $publication,
                    (string) $request->query->get('src'),
                    (string) $request->query->get('dest'),
                );
                break;
            case 'unpublish':
                $this->publicationManager->unpublish($publication, $dimensionAttributes);
                break;
            case 'remove-draft':
                $this->publicationManager->removeDraft($publication, $dimensionAttributes);
                break;
            default:
                throw new RestException(\sprintf('Unrecognized action: %s', $action));
        }

        return View::create($this->normalize($publication, $dimensionAttributes));
    }

    #[Rest\Put(path: '/{id}', name: 'app.admin.put_publication')]
    public function put(Publication $publication, Request $request): View
    {
        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request);

        $this->publicationManager->update($publication, $data, $dimensionAttributes);

        if ('publish' === $this->getAction($request)) {
            $this->publicationManager->publish($publication, $dimensionAttributes);
        }

        return View::create($this->normalize($publication, $dimensionAttributes));
    }

    #[Rest\Delete(path: '/{id}', name: 'app.admin.delete_publication')]
    public function delete(Publication $publication): View
    {
        $this->publicationManager->remove($publication);

        return View::create(null);
    }

    /** @return array<string, mixed> */
    private function getDimensionAttributes(Request $request): array
    {
        return $request->query->all();
    }

    /** @return array<string, mixed> */
    private function getData(Request $request): array
    {
        $data = $request->request->all();

        return $data;
    }

    private function getAction(Request $request): ?string
    {
        return $request->query->get('action', null);
    }

    /**
     * @param array<string, mixed> $dimensionAttributes
     *
     * @return mixed[]
     */
    private function normalize(Publication $publication, array $dimensionAttributes): array
    {
        $dimensionContent = $this->publicationManager->resolve($publication, $dimensionAttributes);

        return $this->publicationManager->normalize($dimensionContent);
    }
}
