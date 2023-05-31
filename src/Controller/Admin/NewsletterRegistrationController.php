<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocaleGetterTrait;
use App\Controller\Trait\RequestActionGetterTrait;
use App\Entity\Api\NewsletterRegistrationRepresentation;
use App\Entity\NewsletterRegistration;
use App\Infrastructure\Sulu\Admin\NewsletterRegistrationAdmin;
use App\Infrastructure\Sulu\Security\SecuredControllerInterface;
use App\Manager\NewsletterRegistrationManager;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        Request $request,
        NewsletterRegistrationManager $manager,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        /** @var NewsletterRegistrationData */
        $data = $request->toArray();

        if (null !== $data['contact']) {
            /** @var User */
            $user = $entityManager->createQueryBuilder()
                ->select('user')
                ->from(User::class, 'user')
                ->where('user.contact = :contactId')
                ->setParameter('contactId', $data['contact'])
                ->getQuery()
                ->getSingleResult();

            $registration = NewsletterRegistration::fromUser($user);
        } else {
            $user = $this->findOneUserByEmail($data['email']);

            $registration = new NewsletterRegistration(
                $data['email'] ?? '',
                $data['locale'] ?? '',
            );
        }

        $manager->create($registration);

        return $this->json(
            data: new NewsletterRegistrationRepresentation($registration, $user),
            status: Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{id}', name: 'put_newsletter_registration', methods: ['PUT'])]
    public function putAction(
        NewsletterRegistration $registration,
        Request $request,
        NewsletterRegistrationManager $manager,
    ): JsonResponse {
        /** @var NewsletterRegistrationData */
        $data = $request->toArray();

        $user = $this->findOneUserByEmail($registration->getEmail());

        if (null !== $data['locale'] && !$user instanceof User) {
            $registration->setLocale($data['locale']);
        }

        $manager->update($registration);

        return $this->json(
            new NewsletterRegistrationRepresentation($registration, $user),
        );
    }

    #[Route(path: '/{id}', name: 'delete_newsletter_registration', methods: ['DELETE'])]
    public function deleteAction(
        NewsletterRegistration $registration,
        NewsletterRegistrationManager $manager,
    ): JsonResponse {
        $manager->remove($registration);

        return $this->json(
            data: null,
            status: Response::HTTP_NO_CONTENT,
        );
    }

    private function findOneUserByEmail(?string $email): ?User
    {
        /** @var User|null */
        $user = $this->userRepository->findOneBy(['email' => $email]);

        return $user;
    }
}
