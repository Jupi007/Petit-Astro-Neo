<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Controller\Trait\LocaleGetterTrait;
use App\Entity\PublicationTypo;
use App\Manager\PublicationTypoManager;
use App\Repository\PublicationTypoRepository;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publication-typos', name: 'app.admin.')]
class PublicationTypoController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;

    public function __construct(
        private readonly PublicationTypoManager $manager,
        private readonly PublicationTypoRepository $repository,
    ) {
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_publication_typo_list', methods: ['GET'])]
    public function getList(Request $request): JsonResponse
    {
        $listRepresentation = $this->repository->createDoctrineListRepresentation(
            $this->getLocale($request),
            $request->query->get('publicationId'),
        );

        return $this->json($listRepresentation->toArray());
    }

    #[Route(path: '/{id}', name: 'get_publication_typo', methods: ['GET'])]
    public function get(PublicationTypo $publicationTypo): JsonResponse
    {
        return $this->json(
            $publicationTypo,
        );
    }

    #[Route(path: '/{id}', name: 'delete_publication_typo', methods: ['DELETE'])]
    public function delete(PublicationTypo $publicationTypo): JsonResponse
    {
        $this->manager->remove($publicationTypo);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
