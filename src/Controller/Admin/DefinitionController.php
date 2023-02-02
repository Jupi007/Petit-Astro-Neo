<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\DefinitionAdmin;
use App\Controller\Trait\LocalizedControllerTrait;
use App\Entity\Api\DefinitionRepresentation;
use App\Entity\Definition;
use App\Manager\DefinitionManager;
use App\Repository\DefinitionRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @phpstan-import-type DefinitionData from DefinitionManager */
#[Route('/admin/api/definitions', name: 'app.admin.')]
class DefinitionController extends AbstractController implements SecuredControllerInterface
{
    use LocalizedControllerTrait;

    public function __construct(
        private readonly DefinitionManager $manager,
        private readonly DefinitionRepository $repository,
    ) {
    }

    public function getSecurityContext(): string
    {
        return DefinitionAdmin::SECURITY_CONTEXT;
    }

    #[Rest\Get(name: 'get_definition_list')]
    public function getList(Request $request): View
    {
        $listRepresentation = $this->repository->createDoctrineListRepresentation(
            $this->getLocale($request),
        );

        return View::create($listRepresentation->toArray());
    }

    #[Rest\Post(name: 'post_definition')]
    public function post(Request $request): View
    {
        /** @var DefinitionData */
        $data = $request->toArray();

        $definition = $this->manager->create(
            $data,
            $this->getLocale($request),
        );

        return View::create(
            new DefinitionRepresentation($definition),
            Response::HTTP_CREATED,
        );
    }

    #[Rest\Get(path: '/{id}', name: 'get_definition')]
    public function get(Definition $definition): View
    {
        return View::create(
            new DefinitionRepresentation($definition),
        );
    }

    #[Rest\Put(path: '/{id}', name: 'put_definition')]
    public function put(Definition $definition, Request $request): View
    {
        /** @var DefinitionData */
        $data = $request->toArray();

        $definition = $this->manager->update(
            $definition,
            $data,
            $this->getLocale($request),
        );

        return View::create(
            new DefinitionRepresentation($definition),
        );
    }

    #[Rest\Delete(path: '/{id}', name: 'delete_definition')]
    public function delete(Definition $definition): View
    {
        $this->manager->remove($definition);

        return View::create(null);
    }
}
