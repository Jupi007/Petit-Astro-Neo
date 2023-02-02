<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Controller\Trait\LocalizedControllerTrait;
use App\Entity\Publication;
use App\Manager\PublicationManager;
use App\Repository\PublicationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publications', name: 'app.admin.')]
class PublicationController extends AbstractController implements SecuredControllerInterface
{
    use LocalizedControllerTrait;

    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly PublicationManager $publicationManager,
        private readonly ContentManagerInterface $contentManager,
    ) {
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Rest\Get(name: 'get_publication_list')]
    public function getList(Request $request): View
    {
        $listRepresentation = $this->publicationRepository->createDoctrineListRepresentation(
            $this->getLocale($request),
        );

        return View::create($listRepresentation->toArray());
    }

    #[Rest\Get(path: '/{id}', name: 'get_publication')]
    public function get(Publication $publication, Request $request): View
    {
        $dimensionAttributes = $this->getDimensionAttributes($request);

        return View::create($this->normalize($publication, $dimensionAttributes));
    }

    #[Rest\Post(name: 'post_publication')]
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

    #[Rest\Post(path: '/{id}', name: 'post_trigger_publication')]
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

    #[Rest\Put(path: '/{id}', name: 'put_publication')]
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

    #[Rest\Delete(path: '/{id}', name: 'delete_publication')]
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
    public function normalize(Publication $publication, array $dimensionAttributes): array
    {
        return $this->contentManager->normalize(
            $this->contentManager->resolve($publication, $dimensionAttributes),
        );
    }
}
