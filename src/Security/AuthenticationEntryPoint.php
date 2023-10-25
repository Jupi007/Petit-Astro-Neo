<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private UrlGeneratorInterface $router,
    ) {
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if ('json' === $request->getRequestFormat()) {
            return new Response(
                status: Response::HTTP_UNAUTHORIZED,
            );
        }

        return new RedirectResponse(
            $this->router->generate(
                name: 'sulu_community.login',
            ),
        );
    }
}
