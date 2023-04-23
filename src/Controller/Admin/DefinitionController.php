<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\DefinitionAdmin;
use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocaleGetterTrait;
use App\Entity\Api\DefinitionRepresentation;
use App\Entity\Definition;
use App\Manager\DefinitionManager;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/definitions', name: 'app.admin.')]
class DefinitionController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;

    public function getSecurityContext(): string
    {
        return DefinitionAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_definition_list', methods: ['GET'])]
    public function getList(
        Request $request,
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ): JsonResponse {
        return $this->json(
            $doctrineListRepresentationFactory->createDoctrineListRepresentation(
                Definition::RESOURCE_KEY,
                parameters: ['locale' => $this->getLocale($request)],
            ),
        );
    }

    #[Route(path: '/{id}', name: 'get_definition', methods: ['GET'])]
    public function get(Definition $definition): JsonResponse
    {
        return $this->json(
            new DefinitionRepresentation($definition),
        );
    }

    #[Route(name: 'post_definition', methods: ['POST'])]
    public function post(
        Request $request,
        DefinitionManager $manager,
    ): JsonResponse {
        $definition = new Definition();

        $this->mapRequestToDefinition($definition, $request);
        $manager->create($definition, $this->getRoutePath($request));

        return $this->json(
            data: new DefinitionRepresentation($definition),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'put_definition', methods: ['PUT'])]
    public function put(
        Definition $definition,
        Request $request,
        DefinitionManager $manager,
    ): JsonResponse {
        $this->mapRequestToDefinition($definition, $request);
        $manager->update($definition, $this->getRoutePath($request));

        return $this->json(
            new DefinitionRepresentation($definition),
        );
    }

    #[Route(path: '/{id}', name: 'delete_definition', methods: ['DELETE'])]
    public function delete(
        Definition $definition,
        DefinitionManager $manager,
    ): JsonResponse {
        $manager->remove($definition);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }

    private function mapRequestToDefinition(Definition $definition, Request $request): void
    {
        /** @var array{
         *   title: string|null,
         *   description: string|null,
         * } */
        $data = $request->toArray();

        $definition
            ->setLocale($this->getLocale($request))
            ->setTitle($data['title'] ?? '')
            ->setDescription($data['description'] ?? '');
    }

    private function getRoutePath(Request $request): string
    {
        /** @var array{
         *   routePath: string|null,
         * } */
        $data = $request->toArray();

        return $data['routePath'] ?? '';
    }
}
