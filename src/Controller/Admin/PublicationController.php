<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Entity\Api\PublicationRepresentation;
use App\Entity\Publication;
use App\Manager\PublicationManager;
use App\Repository\PublicationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publications')]
class PublicationController extends AbstractController implements SecuredControllerInterface
{
    public function __construct(
        private readonly PublicationManager $manager,
        private readonly PublicationRepository $repository,
    ) {
    }

    #[Rest\Get(name: 'app.admin.get_publication_list')]
    public function getList(Request $request): View
    {
        $listRepresentation = $this->repository->createDoctrineListRepresentation($this->getLocale($request));

        return View::create($listRepresentation->toArray());
    }

    #[Rest\Post(name: 'app.admin.post_publication')]
    public function post(Request $request): View
    {
        $publication = $this->manager->createFromRequest($request);

        return View::create(
            new PublicationRepresentation($publication),
            Response::HTTP_CREATED,
        );
    }

    #[Rest\Get(path: '/{id}', name: 'app.admin.get_publication')]
    public function get(Publication $publication): View
    {
        return View::create(
            new PublicationRepresentation($publication),
        );
    }

    #[Rest\Put(path: '/{id}', name: 'app.admin.put_publication')]
    public function put(Publication $publication, Request $request): View
    {
        $publication = $this->manager->updateFromRequest($publication, $request);

        return View::create(
            new PublicationRepresentation($publication),
        );
    }

    #[Rest\Delete(path: '/{id}', name: 'app.admin.delete_publication')]
    public function delete(Publication $publication): View
    {
        $this->manager->remove($publication);

        return View::create(null);
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): string
    {
        return $request->query->get(
            key: 'locale',
            default: '',
        );
    }
}
