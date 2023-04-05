<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Controller\Trait\LocalizationsGetterTrait;
use App\Entity\Api\DefinitionRepresentation;
use App\Entity\Definition;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefinitionController extends AbstractController
{
    use LocalizationsGetterTrait;

    public function __construct(
        private readonly TemplateAttributeResolverInterface $templateAttributeResolver,
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly WebspaceManagerInterface $webspaceManager,
    ) {
    }

    // Controlled by App\Routing\DefinitionRouteDefaultsProvider
    public function index(Request $request, Definition $definition): Response
    {
        $parameters = [
            'localizations' => $this->getLocalizationsArray($definition),
        ];

        if ('json' !== $request->getRequestFormat()) {
            $parameters = $this->templateAttributeResolver->resolve(\array_merge([
                'content' => $definition,
            ], $parameters));

            return $this->render('definition/definition.html.twig', $parameters);
        }

        return $this->json(\array_merge([
            'content' => new DefinitionRepresentation($definition),
        ], $parameters));
    }
}
