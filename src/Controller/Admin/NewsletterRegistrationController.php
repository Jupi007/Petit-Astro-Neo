<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\NewsletterRegistrationAdmin;
use App\Common\DoctrineListRepresentationFactory;
use App\Controller\Trait\LocalizedControllerTrait;
use App\Entity\Api\NewsletterRegistrationRepresentation;
use App\Entity\NewsletterRegistration;
use App\Manager\NewsletterRegistrationManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Component\Security\Authentication\UserRepositoryInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    use LocalizedControllerTrait;

    public function __construct(
        private readonly NewsletterRegistrationManager $manager,
        private readonly EntityManagerInterface $entityManager,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function getSecurityContext(): string
    {
        return NewsletterRegistrationAdmin::SECURITY_CONTEXT;
    }

    #[Rest\Get(name: 'get_newsletter_registration_list')]
    public function getList(): View
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
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

        return View::create($listRepresentation);
    }

    #[Rest\Post(name: 'post_newsletter_registration')]
    public function post(Request $request): View
    {
        /** @var NewsletterRegistrationData */
        $data = $request->toArray();

        if (null !== $data['contact']) {
            /** @var User */
            $user = $this->entityManager->createQueryBuilder()
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

        $this->manager->create($registration);
        $this->entityManager->flush();

        return View::create(
            new NewsletterRegistrationRepresentation($registration, $user),
            Response::HTTP_CREATED,
        );
    }

    #[Rest\Get(path: '/{id}', name: 'get_newsletter_registration')]
    public function get(NewsletterRegistration $registration): View
    {
        $user = $this->findOneUserByEmail($registration->getEmail());

        return View::create(
            new NewsletterRegistrationRepresentation($registration, $user),
        );
    }

    #[Rest\Put(path: '/{id}', name: 'put_newsletter_registration')]
    public function put(NewsletterRegistration $registration, Request $request): View
    {
        /** @var NewsletterRegistrationData */
        $data = $request->toArray();

        $user = $this->findOneUserByEmail($registration->getEmail());

        if (null !== $data['locale'] && null === $user) {
            $registration->setLocale($data['locale']);
        }

        $this->manager->update($registration);
        $this->entityManager->flush();

        return View::create(
            new NewsletterRegistrationRepresentation($registration, $user),
        );
    }

    #[Rest\Delete(path: '/{id}', name: 'delete_newsletter_registration')]
    public function delete(NewsletterRegistration $registration): View
    {
        $this->manager->remove($registration);
        $this->entityManager->flush();

        return View::create(null);
    }

    private function findOneUserByEmail(?string $email): ?User
    {
        /** @var User|null */
        $user = $this->userRepository->findOneBy(['email' => $email]);

        return $user;
    }
}
