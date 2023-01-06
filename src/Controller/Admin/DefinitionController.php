<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Api\DefinitionRepresentation;
use App\Entity\Definition;
use App\Manager\DefinitionManager;
use App\Repository\DefinitionRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/definitions')]
class DefinitionController extends AbstractController
{
    public function __construct(
        private readonly DefinitionManager $manager,
        private readonly DefinitionRepository $repository,
    ) {
    }

    #[Rest\Get(path: '', name: 'app.get_definition_list')]
    public function getListAction(?string $locale): View
    {
        $listRepresentation = $this->repository->createDoctrineListRepresentation($locale ?? '');

        return View::create($listRepresentation->toArray());
    }

    #[Rest\Get(path: '/{id}', name: 'app.get_definition')]
    public function getAction(Definition $definition): View
    {
        return View::create(
            new DefinitionRepresentation($definition),
        );
    }

    #[Rest\Post(path: '', name: 'app.post_definition')]
    public function postAction(Request $request): View
    {
        $definition = $this->manager->createFromRequest($request);

        return View::create(
            new DefinitionRepresentation($definition),
            Response::HTTP_CREATED,
        );
    }

    #[Rest\Put(path: '/{id}', name: 'app.put_definition')]
    public function putAction(Definition $definition, Request $request): View
    {
        $definition = $this->manager->updateFromRequest($definition, $request);

        return View::create(
            new DefinitionRepresentation($definition),
        );
    }

    #[Rest\Delete(path: '/{id}', name: 'app.delete_definition')]
    public function deleteAction(Definition $definition): View
    {
        $this->manager->remove($definition);

        return View::create(null);
    }
}
