<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Controller\Trait\LocalizationsGetterTrait;
use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Entity\PublicationTypo;
use App\Form\PublicationTypoType;
use App\Manager\PublicationTypoManager;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Structure\ContentStructureBridge;
use Sulu\Bundle\HeadlessBundle\Content\StructureResolverInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Component\Content\Compat\PageInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicationController extends AbstractHeadlessWebsiteController
{
    use LocalizationsGetterTrait;

    public function __construct(
        StructureResolverInterface $structureResolver,
        private readonly PublicationTypoManager $manager,
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
        parent::__construct($structureResolver);
    }

    public function indexAction(
        Request $request,
        ContentStructureBridge $structure,
        bool $preview = false,
        bool $partial = false,
    ): Response {
        $typo = new PublicationTypo($this->getPublication($structure));

        $typoForm = $this->createForm(PublicationTypoType::class, $typo);
        $typoForm->handleRequest($request);

        if ($typoForm->isSubmitted() && $typoForm->isValid()) {
            /** @var PublicationTypo */
            $typo = $typoForm->getData();
            $this->manager->create($typo);

            return $this->redirect('?typoSend=true');
        }

        $attributes = [
            'typoForm' => $typoForm->createView(),
            // This is hacky, but needed because ContentBundle doesn't provide valid localizations
            'localizationsOverwrite' => $this->getLocalizationsArray($this->getPublication($structure)),
        ];

        /** @var PageInterface $structure */
        return $this->abstractIndexAction($request, $structure, $preview, $partial, $attributes);
    }

    private function getPublication(ContentStructureBridge $structure): Publication
    {
        /** @var PublicationDimensionContent */
        $publicationDimensionContent = $structure->getContent();

        return $publicationDimensionContent->getResource();
    }
}
