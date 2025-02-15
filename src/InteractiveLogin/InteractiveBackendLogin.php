<?php

declare(strict_types=1);

/*
 * This file is part of Backend Password Recovery Bundle.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/backend-password-recovery-bundle
 */

namespace Markocupic\BackendPasswordRecoveryBundle\InteractiveLogin;

use Contao\BackendUser;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\User\ContaoUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class InteractiveLogin.
 */
class InteractiveBackendLogin
{
    public const SECURED_AREA_BACKEND = 'contao_backend';

    private ContaoFramework $framework;
    private SessionInterface $session;
    private TokenStorageInterface $tokenStorage;
    private EventDispatcherInterface $eventDispatcher;
    private RequestStack $requestStack;
    private ?LoggerInterface $logger;

    public function __construct(ContaoFramework $framework, SessionInterface $session, TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, LoggerInterface $logger = null)
    {
        $this->framework = $framework;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public function login(string $username): bool
    {
        $this->framework->initialize();

        $strFirewall = static::SECURED_AREA_BACKEND;

        $userClass = BackendUser::class;

        // Retrieve user by its username
        $userProvider = new ContaoUserProvider($this->framework, $this->session, $userClass, $this->logger);

        $user = $userProvider->loadUserByIdentifier($username);

        $token = new UsernamePasswordToken($user, null, $strFirewall, $user->getRoles());

        if (!is_a($token, UsernamePasswordToken::class)) {
            return false;
        }

        $this->tokenStorage->setToken($token);

        // Save the token to the session
        $this->session->set('_security_'.self::SECURED_AREA_BACKEND, serialize($token));
        $this->session->save();

        /** @var InteractiveLoginEvent $event */
        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $token);
        $this->eventDispatcher->dispatch($event, 'security.interactive_login');

        if (!is_a($event, InteractiveLoginEvent::class)) {
            return false;
        }

        /** @var BackendUser $user */
        $user = $token->getUser();

        if ($user instanceof BackendUser) {
            if ($username === $user->username) {
                return true;
            }
        }

        return false;
    }
}
