<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Controller\Trait\LocaleGetterTrait;
use App\Entity\ContactRequest;
use App\Manager\ContactRequestManager;
use App\Repository\ContactRequestRepository;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publication-typos', name: 'app.admin.')]
class ContactRequestController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;

    public function __construct(
        private readonly ContactRequestManager $manager,
        private readonly ContactRequestRepository $repository,
    ) {
    }

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_contact_request_list', methods: ['GET'])]
    public function getList(): JsonResponse
    {
        $listRepresentation = $this->repository->createDoctrineListRepresentation();

        return $this->json($listRepresentation->toArray());
    }

    #[Route(path: '/{id}', name: 'get_contact_request', methods: ['GET'])]
    public function get(ContactRequest $publicationTypo): JsonResponse
    {
        return $this->json($publicationTypo);
    }

    #[Route(path: '/{id}', name: 'delete_contact_request', methods: ['DELETE'])]
    public function delete(ContactRequest $publicationTypo): JsonResponse
    {
        $this->manager->remove($publicationTypo);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
