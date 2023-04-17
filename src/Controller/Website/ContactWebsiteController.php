<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\ContactRequest;
use App\Exception\NullAssertionException;
use App\Form\ContactRequestType;
use App\Form\Data\ContactRequestTypeData;
use App\Manager\ContactRequestManager;
use Sulu\Bundle\HeadlessBundle\Content\StructureResolverInterface;
use Sulu\Bundle\WebsiteBundle\Twig\Content\ContentPathInterface;
use Sulu\Component\Content\Compat\PageInterface;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactWebsiteController extends AbstractHeadlessWebsiteController
{
    public function __construct(
        StructureResolverInterface $structureResolver,
        private readonly ContactRequestManager $manager,
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
        $contactForm = $this->createForm(ContactRequestType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            /** @var ContactRequestTypeData */
            $data = $contactForm->getData();

            $registration = new ContactRequest(
                object: $data->object ?? throw new NullAssertionException(),
                email: $data->email ?? throw new NullAssertionException(),
                message: $data->message ?? throw new NullAssertionException(),
            );

            $this->manager->create($registration);

            $this->addFlash(
                'success',
                'app.contact_form.success_message',
            );

            return $this->redirect($this->contentPath->getContentRootPath());
        }

        /** @var PageInterface $structure */
        return $this->abstractIndexAction($request, $structure, $preview, $partial, [
            'contactForm' => $contactForm,
        ]);
    }
}
