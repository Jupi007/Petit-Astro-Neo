<?php

declare(strict_types=1);

namespace App\UserInterface\Controller\Admin;

use App\Application\Manager\PublicationManager;
use App\Domain\Entity\Publication;
use App\Infrastructure\Sulu\Admin\PublicationAdmin;
use App\Infrastructure\Sulu\Security\SecuredControllerInterface;
use App\UserInterface\Controller\Trait\LocaleGetterTrait;
use App\UserInterface\Controller\Trait\RequestActionGetterTrait;
use App\UserInterface\DoctrineListRepresentationFactory;
use Sulu\Bundle\ContentBundle\Content\Application\ContentManager\ContentManagerInterface;
use Sulu\Component\Rest\Exception\RestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
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
        #[MapQueryParameter] string $locale,
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ): JsonResponse {
        return $this->json(
            $doctrineListRepresentationFactory->createDoctrineListRepresentation(
                Publication::RESOURCE_KEY,
                parameters: ['locale' => $locale],
                includedFields: ['locale', 'ghostLocale'],
            )->toArray(),
        );
    }

    #[Route(path: '/{id}', name: 'get_publication', methods: ['GET'])]
    public function getAction(
        Publication $publication,
        #[MapQueryParameter] string $locale,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        return $this->json(
            $this->normalize(
                $contentManager,
                $publication,
                $locale,
            ),
        );
    }

    #[Route(name: 'post_publication', methods: ['POST'])]
    public function postAction(
        #[MapQueryParameter] string $locale,
        Request $request,
        PublicationManager $publicationManager,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        $publication = $publicationManager->create(
            $this->getData($request),
            $locale,
        );

        if ('publish' === $this->getRequestAction($request)) {
            $publicationManager->publish(
                (int) $publication->getId(),
                $locale,
            );
        }

        return $this->json(
            data: $this->normalize(
                $contentManager,
                $publication,
                $locale,
            ),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'post_trigger_publication', methods: ['POST'])]
    public function postTriggerAction(
        int $id,
        #[MapQueryParameter] string $locale,
        Request $request,
        PublicationManager $publicationManager,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        $action = $this->getRequestAction($request);

        match ($action) {
            'copy-locale' => $publication = $publicationManager->copyLocale(
                $id,
                (string) $request->query->get('src'),
                (string) $request->query->get('dest'),
            ),
            'unpublish' => $publication = $publicationManager->unpublish($id, $locale),
            'remove-draft' => $publication = $publicationManager->removeDraft($id, $locale),
            'notify' => $publication = $publicationManager->notify($id),
            default => throw new RestException(\sprintf('Unrecognized action: %s', $action)),
        };

        return $this->json(
            $this->normalize(
                $contentManager,
                $publication,
                $locale,
            ),
        );
    }

    #[Route(path: '/{id}', name: 'put_publication', methods: ['PUT'])]
    public function putAction(
        int $id,
        #[MapQueryParameter] string $locale,
        Request $request,
        PublicationManager $publicationManager,
        ContentManagerInterface $contentManager,
    ): JsonResponse {
        $publication = $publicationManager->update(
            $id,
            $this->getData($request),
            $locale,
        );

        if ('publish' === $this->getRequestAction($request)) {
            $publication = $publicationManager->publish($id, $locale);
        }

        return $this->json(
            $this->normalize(
                $contentManager,
                $publication,
                $locale,
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
    private function getData(Request $request): array
    {
        return $request->request->all();
    }

    /** @return mixed[] */
    public function normalize(
        ContentManagerInterface $contentManager,
        Publication $publication,
        string $locale,
    ): array {
        $data = $contentManager->normalize(
            $contentManager->resolve($publication, ['locale' => $locale]),
        );

        return \array_merge($data, [
            'notified' => $publication->isNotified(),
        ]);
    }
}
