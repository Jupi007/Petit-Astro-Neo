<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocaleGetterTrait;
use App\Controller\Trait\RequestActionGetterTrait;
use App\Entity\Publication;
use App\Infrastructure\Sulu\Admin\PublicationAdmin;
use App\Infrastructure\Sulu\Security\SecuredControllerInterface;
use App\Manager\PublicationManager;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Component\Rest\Exception\RestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publications', name: 'app.admin.')]
class PublicationController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;
    use RequestActionGetterTrait;

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_publication_list', methods: ['GET'])]
    public function getListAction(
        Request $request,
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ): JsonResponse {
        return $this->json(
            $doctrineListRepresentationFactory->createDoctrineListRepresentation(
                Publication::RESOURCE_KEY,
                parameters: ['locale' => $this->getLocale($request)],
                includedFields: ['locale', 'ghostLocale'],
            )->toArray(),
        );
    }

    #[Route(path: '/{id}', name: 'get_publication', methods: ['GET'])]
    public function getAction(
        Publication $publication,
        Request $request,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        return $this->json(
            $this->normalize(
                $contentManager,
                $publication,
                $this->getDimensionAttributes($request),
            ),
        );
    }

    #[Route(name: 'post_publication', methods: ['POST'])]
    public function postAction(
        Request $request,
        PublicationManager $publicationManager,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request);

        $publication = $publicationManager->create($data, $dimensionAttributes);

        if ('publish' === $this->getRequestAction($request)) {
            $publicationManager->publish((int) $publication->getId(), $dimensionAttributes);
        }

        return $this->json(
            data: $this->normalize(
                $contentManager,
                $publication,
                $dimensionAttributes,
            ),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'post_trigger_publication', methods: ['POST'])]
    public function postTriggerAction(
        int $id,
        Request $request,
        PublicationManager $publicationManager,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        $dimensionAttributes = $this->getDimensionAttributes($request);
        $action = $this->getRequestAction($request);

        match ($action) {
            'copy-locale' => $publication = $publicationManager->copyLocale(
                $id,
                (string) $request->query->get('src'),
                (string) $request->query->get('dest'),
            ),
            'unpublish' => $publication = $publicationManager->unpublish($id, $dimensionAttributes),
            'remove-draft' => $publication = $publicationManager->removeDraft($id, $dimensionAttributes),
            'notify' => $publication = $publicationManager->notify($id),
            default => throw new RestException(\sprintf('Unrecognized action: %s', $action)),
        };

        return $this->json(
            $this->normalize(
                $contentManager,
                $publication,
                $dimensionAttributes,
            ),
        );
    }

    #[Route(path: '/{id}', name: 'put_publication', methods: ['PUT'])]
    public function putAction(
        int $id,
        Request $request,
        PublicationManager $publicationManager,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        $data = $this->getData($request);
        $dimensionAttributes = $this->getDimensionAttributes($request);

        $publication = $publicationManager->update($id, $data, $dimensionAttributes);

        if ('publish' === $this->getRequestAction($request)) {
            $publication = $publicationManager->publish($id, $dimensionAttributes);
        }

        return $this->json(
            $this->normalize(
                $contentManager,
                $publication,
                $dimensionAttributes,
            ),
        );
    }

    #[Route(path: '/{id}', name: 'delete_publication', methods: ['DELETE'])]
    public function deleteAction(
        int $id,
        PublicationManager $publicationManager,
    ): JsonResponse {
        $publicationManager->remove($id);

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

    /**
     * @param array<string, mixed> $dimensionAttributes
     *
     * @return mixed[]
     */
    public function normalize(
        ContentManagerInterface $contentManager,
        Publication $publication,
        array $dimensionAttributes,
    ): array {
        $data = $contentManager->normalize(
            $contentManager->resolve($publication, $dimensionAttributes),
        );

        return \array_merge($data, [
            'notified' => $publication->isNotified(),
        ]);
    }
}
