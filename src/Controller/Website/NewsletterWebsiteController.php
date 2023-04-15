<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\NewsletterRegistration;
use App\Exception\NullAssertionException;
use App\Form\Data\NewsletterRegistrationTypeData;
use App\Form\NewsletterRegistrationType;
use App\Manager\NewsletterRegistrationManager;
use Sulu\Bundle\HeadlessBundle\Content\StructureResolverInterface;
use Sulu\Bundle\WebsiteBundle\Twig\Content\ContentPathInterface;
use Sulu\Component\Content\Compat\PageInterface;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterWebsiteController extends AbstractHeadlessWebsiteController
{
    public function __construct(
        StructureResolverInterface $structureResolver,
        private readonly NewsletterRegistrationManager $manager,
        #[Autowire('@sulu_website.twig.content_path')]
        private readonly ContentPathInterface $contentPath,
    ) {
        parent::__construct($structureResolver);
    }

    public function indexAction(
        Request $request,
        StructureInterface $structure,
        bool $preview = false,
        bool $partial = false,
    ): Response {
        $registrationForm = $this->createForm(NewsletterRegistrationType::class);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            /** @var NewsletterRegistrationTypeData */
            $data = $registrationForm->getData();

            $registration = new NewsletterRegistration(
                email: $data->email ?? throw new NullAssertionException(),
                locale: $data->locale ?? throw new NullAssertionException(),
            );
            $this->manager->create($registration);

            $this->addFlash('success', 'app.newsletter_form.success_message');

            return $this->redirect($this->contentPath->getContentRootPath());
        }

        /** @var PageInterface $structure */
        return $this->abstractIndexAction($request, $structure, $preview, $partial, [
            'registrationForm' => $registrationForm,
        ]);
    }
}
