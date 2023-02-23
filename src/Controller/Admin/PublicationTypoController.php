<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Controller\Trait\LocalizedControllerTrait;
use App\Entity\PublicationTypo;
use App\Manager\PublicationTypoManager;
use App\Repository\PublicationTypoRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publication-typos', name: 'app.admin.')]
class PublicationTypoController extends AbstractController implements SecuredControllerInterface
{
    use LocalizedControllerTrait;

    public function __construct(
        private readonly PublicationTypoManager $manager,
        private readonly PublicationTypoRepository $repository,
    ) {
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Rest\Get(name: 'get_publication_typo_list')]
    public function getList(Request $request): View
    {
        $listRepresentation = $this->repository->createDoctrineListRepresentation(
            $request->query->get('publicationId'),
        );

        return View::create($listRepresentation->toArray());
    }

    #[Rest\Get(path: '/{id}', name: 'get_publication_typo')]
    public function get(PublicationTypo $publicationTypo): View
    {
        return View::create(
            $publicationTypo,
        );
    }

    #[Rest\Delete(path: '/{id}', name: 'delete_publication_typo')]
    public function delete(PublicationTypo $publicationTypo): View
    {
        $this->manager->remove($publicationTypo);

        return View::create(null);
    }
}
