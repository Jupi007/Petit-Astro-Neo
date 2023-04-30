<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\PublicationAdmin;
use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocaleGetterTrait;
use App\Controller\Trait\RequestActionGetterTrait;
use App\Entity\ContactRequest;
use App\Manager\ContactRequestManager;
use App\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api/publication-typos', name: 'app.admin.')]
class ContactRequestController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;
    use RequestActionGetterTrait;

    public function getSecurityContext(): string
    {
        return PublicationAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_contact_request_list', methods: ['GET'])]
    public function getListAction(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ): JsonResponse {
        return $this->json(
            $doctrineListRepresentationFactory->createDoctrineListRepresentation(
                ContactRequest::RESOURCE_KEY,
            )->toArray(),
        );
    }

    #[Route(path: '/{id}', name: 'get_contact_request', methods: ['GET'])]
    public function getAction(ContactRequest $publicationTypo): JsonResponse
    {
        return $this->json($publicationTypo);
    }

    #[Route(path: '/{id}', name: 'delete_contact_request', methods: ['DELETE'])]
    public function deleteAction(
        ContactRequest $publicationTypo,
        ContactRequestManager $manager,
    ): JsonResponse {
        $manager->remove($publicationTypo);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }
}
