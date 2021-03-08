<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Security;

use App\Form\AdminLoginForm;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

final class AppLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        $route = $request->attributes->get('_route');

        return
            'app_login' === $route &&
            'POST' === $request->getMethod();
    }

    public function getCredentials(Request $request): array
    {
        $form = $this->formFactory->create(AdminLoginForm::class);

        $form->handleRequest($request);

        $data = $form->getData();

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['username']
        );

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
