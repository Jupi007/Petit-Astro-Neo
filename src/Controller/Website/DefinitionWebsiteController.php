<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\API\Response\DefinitionResponse;
use App\Controller\Common\LocalizationsGetterTrait;
use App\Repository\DefinitionRepositoryInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefinitionWebsiteController extends AbstractController
{
    use LocalizationsGetterTrait;

    public function __construct(
        private readonly TemplateAttributeResolverInterface $templateAttributeResolver,
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly WebspaceManagerInterface $webspaceManager,
        private readonly DefinitionRepositoryInterface $definitionRepository,
    ) {
    }

    // Controlled by App\Routing\DefinitionRouteDefaultsProvider
    public function index(Request $request, int $id, string $locale): Response
    {
        $definition = $this->definitionRepository->getOneLocalized($id, $locale);

        $parameters = [
            'localizations' => $this->getLocalizationsArray($definition),
        ];

        if ('json' !== $request->getRequestFormat()) {
            $parameters = $this->templateAttributeResolver->resolve(['content' => $definition, ...$parameters]);

            return $this->render('definition/definition.html.twig', $parameters);
        }

        return $this->json(['content' => new DefinitionResponse($definition), ...$parameters]);
    }
}
