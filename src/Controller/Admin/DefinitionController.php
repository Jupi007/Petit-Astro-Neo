<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\API\Representation\DefinitionRepresentation;
use App\API\Request\Definition\CreateDefinitionRequest;
use App\API\Request\Definition\UpdateDefinitionRequest;
use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocaleGetterTrait;
use App\Controller\Trait\RequestActionGetterTrait;
use App\DTO\Definition\CreateDefinitionDTO;
use App\DTO\Definition\UpdateDefinitionDTO;
use App\Entity\Definition;
use App\Manager\DefinitionManager;
use App\Repository\DefinitionRepositoryInterface;
use App\Sulu\Admin\DefinitionAdmin;
use App\Sulu\Security\SecuredControllerInterface;
use Sulu\Component\Rest\Exception\RestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/definitions', name: 'app.admin.')]
class DefinitionController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;
    use RequestActionGetterTrait;

    public function getSecurityContext(): string
    {
        return DefinitionAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_definition_list', methods: ['GET'])]
    public function getListAction(
        Request $request,
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ): JsonResponse {
        return $this->json(
            $doctrineListRepresentationFactory->createDoctrineListRepresentation(
                Definition::RESOURCE_KEY,
                parameters: ['locale' => $this->getLocale($request)],
            )->toArray(),
        );
    }

    #[Route(path: '/{id}', name: 'get_definition', methods: ['GET'])]
    public function getAction(
        int $id,
        #[MapQueryParameter] string $locale,
        DefinitionRepositoryInterface $repository,
    ): JsonResponse {
        $definition = $repository->getOneLocalized($id, $locale);

        return $this->json(
            new DefinitionRepresentation($definition),
        );
    }

    #[Route(name: 'post_definition', methods: ['POST'])]
    public function postAction(
        #[MapRequestPayload] CreateDefinitionRequest $request,
        #[MapQueryParameter] string $locale,
        DefinitionManager $manager,
    ): JsonResponse {
        $definition = $manager->create(
            new CreateDefinitionDTO(
                title: $request->title,
                description: $request->description,
                routePath: $request->routePath,
                locale: $locale,
            ),
        );

        return $this->json(
            data: new DefinitionRepresentation($definition),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'post_trigger_definition', methods: ['POST'])]
    public function postTriggerAction(
        int $id,
        Request $request,
        DefinitionManager $manager,
    ): JsonResponse {
        $action = $this->getRequestAction($request);

        match ($action) {
            'copy-locale' => $definition = $manager->copyLocale(
                $id,
                (string) $request->query->get('src'),
                (string) $request->query->get('dest'),
            ),
            default => throw new RestException(\sprintf('Unrecognized action: %s', $action)),
        };

        return $this->json(
            data: new DefinitionRepresentation($definition),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'put_definition', methods: ['PUT'])]
    public function putAction(
        int $id,
        #[MapRequestPayload] UpdateDefinitionRequest $request,
        #[MapQueryParameter] string $locale,
        DefinitionManager $manager,
    ): JsonResponse {
        $definition = $manager->update(
            new UpdateDefinitionDTO(
                id: $id,
                title: $request->title,
                description: $request->description,
                routePath: $request->routePath,
                locale: $locale,
            ),
        );

        return $this->json(
            new DefinitionRepresentation($definition),
        );
    }

    #[Route(path: '/{id}', name: 'delete_definition', methods: ['DELETE'])]
    public function deleteAction(
        int $id,
        DefinitionManager $manager,
    ): JsonResponse {
        $manager->remove($id);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
