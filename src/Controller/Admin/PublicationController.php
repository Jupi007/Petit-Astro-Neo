<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Controller\Trait\LocaleGetterTrait;
use App\Entity\Publication;
use App\Manager\PublicationManager;
use App\Repository\PublicationRepository;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publications', name: 'app.admin.')]
class PublicationController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;

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

    #[Route(name: 'get_publication_list', methods: ['GET'])]
    public function getList(Request $request): JsonResponse
    {
        $listRepresentation = $this->publicationRepository->createDoctrineListRepresentation(
            $this->getLocale($request),
        );

        return $this->json($listRepresentation->toArray());
    }

    #[Route(path: '/{id}', name: 'get_publication', methods: ['GET'])]
    public function get(Publication $publication, Request $request): JsonResponse
    {
        $dimensionAttributes = $this->getDimensionAttributes($request);

        return $this->json($this->normalize($publication, $dimensionAttributes));
    }

    #[Route(name: 'post_publication', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request);

        $publication = $this->publicationManager->create($data, $dimensionAttributes);

        if ('publish' === $this->getAction($request)) {
            $this->publicationManager->publish($publication, $dimensionAttributes);
        }

        return $this->json(
            data: $this->normalize($publication, $dimensionAttributes),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'post_trigger_publication', methods: ['POST'])]
    public function postTrigger(Publication $publication, Request $request): JsonResponse
    {
        $dimensionAttributes = $this->getDimensionAttributes($request);
        $action = $this->getAction($request);

        match ($action) {
            'copy-locale' => $this->publicationManager->copyLocale(
                $publication,
                (string) $request->query->get('src'),
                (string) $request->query->get('dest'),
            ),
            'unpublish' => $this->publicationManager->unpublish($publication, $dimensionAttributes),
            'remove-draft' => $this->publicationManager->removeDraft($publication, $dimensionAttributes),
            'notify' => $this->publicationManager->notify($publication),
            default => throw new RestException(\sprintf('Unrecognized action: %s', $action)),
        };

        return $this->json($this->normalize($publication, $dimensionAttributes));
    }

    #[Route(path: '/{id}', name: 'put_publication', methods: ['PUT'])]
    public function put(Publication $publication, Request $request): JsonResponse
    {
        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request);

        $this->publicationManager->update($publication, $data, $dimensionAttributes);

        if ('publish' === $this->getAction($request)) {
            $this->publicationManager->publish($publication, $dimensionAttributes);
        }

        return $this->json($this->normalize($publication, $dimensionAttributes));
    }

    #[Route(path: '/{id}', name: 'delete_publication', methods: ['DELETE'])]
    public function delete(Publication $publication): JsonResponse
    {
        $this->publicationManager->remove($publication);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }

    /** @return array<string, mixed> */
    private function getDimensionAttributes(Request $request): array
    {
        return $request->query->all();
    }

    /** @return array<string, mixed> */
    private function getData(Request $request): array
    {
        return $request->request->all();
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
        $data = $this->contentManager->normalize(
            $this->contentManager->resolve($publication, $dimensionAttributes),
        );

        return \array_merge($data, [
            'notified' => $publication->isNotified(),
        ]);
    }
}
