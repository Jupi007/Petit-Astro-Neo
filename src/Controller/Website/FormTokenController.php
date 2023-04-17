<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

// Heavily inspired by Sulu\Bundle\FormBundle\Controller\FormTokenController
class FormTokenController extends AbstractController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function tokenAction(Request $request): Response
    {
        /** @var string $formName */
        $formName = $request->attributes->get('form');
        $csrfToken = $this->csrfTokenManager->getToken($formName)->getValue();

        if ($request->get('html')) {
            $response = $this->render('form/form_token_input.html.twig', [
                'formName' => $formName,
                'csrfToken' => $csrfToken,
            ]);
        } else {
            $response = new Response($csrfToken);
        }

        // Deactivate Cache for this token action
        $response->setSharedMaxAge(0);
        $response->setMaxAge(0);
        // Set shared will set the request to public so it need to be done after shared max set to 0
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }
}
