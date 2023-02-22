<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Publication;
use App\Entity\PublicationDimensionContent;
use App\Entity\PublicationTypo;
use App\Form\PublicationTypoType;
use Sulu\Bundle\ContentBundle\Content\Infrastructure\Sulu\Structure\ContentStructureBridge;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicationController extends WebsiteController
{
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
            $typo = $typoForm->getData();
        }

        $attributes = [
            'typoForm' => $typoForm->createView(),
        ];

        return $this->renderStructure($structure, $attributes, $preview, $partial);
    }

    private function getPublication(ContentStructureBridge $structure): Publication
    {
        /** @var PublicationDimensionContent */
        $publicationDimensionContent = $structure->getContent();

        return $publicationDimensionContent->getResource();
    }
}
