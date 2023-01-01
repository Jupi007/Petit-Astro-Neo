<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Definition;
use App\Repository\DefinitionRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Component\Rest\AbstractRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @phpstan-type DefinitionData array{
 *     id: int|null,
 *     title: string|null,
 *     content: string|null,
 * }
 */
class DefinitionController extends AbstractRestController
{
    public function __construct(
        ViewHandlerInterface $viewHandler,
        TokenStorageInterface $tokenStorage,
        private readonly DefinitionRepository $definitionRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
        parent::__construct($viewHandler, $tokenStorage);
    }

    #[Rest\Get(path: '/admin/api/definitions/{id}', name: 'app.get_definition')]
    public function getAction(int $id, Request $request): View
    {
        $definition = $this->load($id, $request);
        if (!$definition instanceof Definition) {
            throw new NotFoundHttpException();
        }

        return $this->view($this->getDataForEntity($definition));
    }

    #[Rest\Put(path: '/admin/api/definitions/{id}', name: 'app.put_definition')]
    public function putAction(int $id, Request $request): View
    {
        $definition = $this->load($id, $request);
        if (!$definition instanceof Definition) {
            throw new NotFoundHttpException();
        }

        /** @var DefinitionData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $definition);
        $this->save($definition);

        return $this->view($this->getDataForEntity($definition));
    }

    #[Rest\Post(path: '/admin/api/definitions', name: 'app.post_definition')]
    public function postAction(Request $request): View
    {
        $definition = $this->create($request);

        /** @var DefinitionData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $definition);
        $this->save($definition);

        return $this->view($this->getDataForEntity($definition), 201);
    }

    #[Rest\Delete(path: '/admin/api/definitions/{id}', name: 'app.delete_definition')]
    public function deleteAction(Definition $definition): View
    {
        $this->remove($definition);

        return $this->view(null);
    }

    #[Rest\Get(path: '/admin/api/definitions', name: 'app.get_definition_list')]
    public function getListAction(Request $request): View
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Definition::RESOURCE_KEY,
            [],
            ['locale' => $this->getLocale($request)],
        );

        return $this->view($listRepresentation->toArray());
    }

    /**
     * @return DefinitionData
     */
    protected function getDataForEntity(Definition $definition): array
    {
        return [
            'id' => $definition->getId(),
            'title' => $definition->getTitle(),
            'content' => $definition->getContent(),
        ];
    }

    /**
     * @param DefinitionData $data
     */
    protected function mapDataToEntity($data, Definition $definition): void
    {
        $definition->setTitle($data['title'] ?? '');
        $definition->setContent($data['content'] ?? '');
    }

    protected function load(int $id, Request $request): ?Definition
    {
        return $this->definitionRepository->findById($id, $this->getLocale($request));
    }

    protected function create(Request $request): Definition
    {
        return $this->definitionRepository->create($this->getLocale($request));
    }

    protected function save(Definition $definition): void
    {
        $this->definitionRepository->save($definition);
    }

    protected function remove(Definition $definition): void
    {
        $this->definitionRepository->remove($definition);
    }
}
