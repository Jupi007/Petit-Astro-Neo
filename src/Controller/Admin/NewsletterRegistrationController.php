<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\API\Representation\NewsletterRegistrationRepresentation;
use App\API\Request\NewsletterRegistration\CreateNewsletterRegistrationRequest;
use App\API\Request\NewsletterRegistration\UpdateNewsletterRegistrationRequest;
use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocaleGetterTrait;
use App\Controller\Trait\RequestActionGetterTrait;
use App\DTO\NewsletterRegistration\CreateNewsletterRegistrationDTO;
use App\DTO\NewsletterRegistration\UpdateNewsletterRegistrationDTO;
use App\Entity\NewsletterRegistration;
use App\Exception\NullAssertionException;
use App\Infrastructure\Sulu\Admin\NewsletterRegistrationAdmin;
use App\Infrastructure\Sulu\Security\SecuredControllerInterface;
use App\Manager\NewsletterRegistrationManager;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

/** @phpstan-type NewsletterRegistrationData array{
 *      email: string|null,
 *      locale: string|null,
 *      contact: int|null
 * } */
#[Route('/admin/api/newsletter-registrations', name: 'app.admin.')]
class NewsletterRegistrationController extends AbstractController implements SecuredControllerInterface
{
    use LocaleGetterTrait;
    use RequestActionGetterTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function getSecurityContext(): string
    {
        return NewsletterRegistrationAdmin::SECURITY_CONTEXT;
    }

    #[Route(name: 'get_newsletter_registration_list', methods: ['GET'])]
    public function getListAction(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ): JsonResponse {
        $listRepresentation = $doctrineListRepresentationFactory->createDoctrineListRepresentation(
            resourceKey: NewsletterRegistration::RESOURCE_KEY,
            itemsCallback: function (array $items) {
                /** @var User[] */
                $users = $this->userRepository->findBy([
                    'email' => \array_map(fn ($item): string => $item['email'], $items),
                ]);
                $usersEmailFullName = [];

                foreach ($users as $key => &$user) {
                    $usersEmailFullName[$user->getEmail()] = $user->getFullName();
                    unset($users[$key]);
                }

                foreach ($items as &$item) {
                    if (isset($usersEmailFullName[$item['email']])) {
                        $item['user'] = $usersEmailFullName[$item['email']];
                    }
                }

                return $items;
            },
        );

        return $this->json($listRepresentation->toArray());
    }

    #[Route(path: '/{id}', name: 'get_newsletter_registration', methods: ['GET'])]
    public function getAction(NewsletterRegistration $registration): JsonResponse
    {
        $user = $this->findOneUserByEmail($registration->getEmail());

        return $this->json(
            new NewsletterRegistrationRepresentation($registration, $user),
        );
    }

    #[Route(name: 'post_newsletter_registration', methods: ['POST'])]
    public function postAction(
        #[MapRequestPayload] CreateNewsletterRegistrationRequest $request,
        NewsletterRegistrationManager $manager,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        if (null !== $request->contact) {
            /** @var User */
            $user = $entityManager->createQueryBuilder()
                ->select('user')
                ->from(User::class, 'user')
                ->where('user.contact = :contactId')
                ->setParameter('contactId', $request->contact)
                ->getQuery()
                ->getSingleResult();

            $dto = new CreateNewsletterRegistrationDTO(
                email: $user->getContact()->getMainEmail() ?? throw new NullAssertionException(),
                locale: $user->getLocale(),
            );
        } elseif (
            null !== $request->email
            && null !== $request->locale
        ) {
            $user = $this->findOneUserByEmail($request->email);
            $dto = new CreateNewsletterRegistrationDTO(
                email: $request->email,
                locale: $request->locale,
            );
        } else {
            throw new \LogicException('Error Processing Request');
        }

        $registration = $manager->create($dto);

        return $this->json(
            data: new NewsletterRegistrationRepresentation($registration, $user),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'put_newsletter_registration', methods: ['PUT'])]
    public function putAction(
        int $id,
        #[MapRequestPayload] UpdateNewsletterRegistrationRequest $request,
        NewsletterRegistrationManager $manager,
    ): JsonResponse {
        $registration = $manager->update(
            new UpdateNewsletterRegistrationDTO(
                id: $id,
                locale: $request->locale,
            ),
        );

        return $this->json(
            new NewsletterRegistrationRepresentation(
                $registration,
                $this->findOneUserByEmail($registration->getEmail()),
            ),
        );
    }

    #[Route(path: '/{id}', name: 'delete_newsletter_registration', methods: ['DELETE'])]
    public function deleteAction(
        int $id,
        NewsletterRegistrationManager $manager,
    ): JsonResponse {
        $manager->remove($id);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }

    private function findOneUserByEmail(?string $email): ?User
    {
        /** @var User|null */
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}
