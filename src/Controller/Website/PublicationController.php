<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Publication;
use Sulu\Bundle\PreviewBundle\Preview\Preview;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PublicationController extends AbstractController
{
    public function __construct(private readonly Environment $twig)
    {
    }

    // Controlled by App\Routing\PublicationRouteDefaultsProvider
    /** @param mixed[] $attributes */
    public function index(Publication $publication, array $attributes = [], bool $preview = false, bool $partial = false): Response
    {
        $template = 'publication/publication.html.twig';
        $parameters = ['content' => $publication];

        if ($partial) {
            $content = $this->renderBlock(
                $template,
                'content',
                $parameters,
            );
        } elseif ($preview) {
            $content = $this->renderPreview(
                $template,
                $parameters,
            );
        } else {
            $content = $this->renderView(
                $template,
                $parameters,
            );
        }

        return new Response($content);
    }

    /** @param mixed[] $parameters */
    protected function renderPreview(string $view, array $parameters = []): string
    {
        $parameters['previewParentTemplate'] = $view;
        $parameters['previewContentReplacer'] = Preview::CONTENT_REPLACER;

        return $this->renderView('@SuluWebsite/Preview/preview.html.twig', $parameters);
    }

    /** @param mixed[] $parameters */
    protected function renderBlock(string $template, string $block, array $parameters = []): string
    {
        $parameters = $this->twig->mergeGlobals($parameters);
        $template = $this->twig->load($template);

        $level = \ob_get_level();
        \ob_start();

        try {
            $rendered = $template->renderBlock($block, $parameters);
            \ob_end_clean();

            return $rendered;
        } catch (\Exception $e) {
            while (\ob_get_level() > $level) {
                \ob_end_clean();
            }

            throw $e;
        }
    }
}
