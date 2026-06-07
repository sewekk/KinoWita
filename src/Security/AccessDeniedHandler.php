<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null || !is_object($token->getUser())) {
            $request->getSession()->getFlashBag()->add('error', 'Musisz być zalogowany, aby uzyskać dostęp do tej strony.');

            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }

        $roles = $token->getUser()->getRoles();

        $request->getSession()->getFlashBag()->add('error', 'Nie masz uprawnień do tej strony.');

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
        }

        if (in_array('ROLE_STAFF', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_staff_dashboard'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }
}
