<?php

declare(strict_types=1);

namespace App\Infrastructure\Sulu\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controllers implementing this interface security will be automatically secured on per-object basis.
 * Modified version of Sulu\Component\Security\Authorization\AccessControl\SecuredObjectControllerInterface.
 */
interface SecuredObjectControllerInterface
{
    /**
     * Returns the class name of the object to check.
     */
    public function getSecuredClass(): string;

    /**
     * Returns the id of the object to check.
     */
    public function getSecuredObjectId(Request $request): string;

    /**
     * Returns the locale for the given request.
     */
    public function getLocale(Request $request): ?string;

    /**
     * Returns the action for the given request.
     */
    public function getRequestAction(Request $request): ?string;
}
