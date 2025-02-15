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

namespace Markocupic\BackendPasswordRecoveryBundle\Listener\ContaoHooks;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ParseTemplate.
 */

/**
 * Class ParseTemplate.
 *
 * @Hook(ParseTemplate::HOOK)
 */
class ParseTemplate
{
    public const HOOK = 'parseTemplate';

    private RequestStack $requestStack;
    private Environment $twig;
    private TranslatorInterface $translator;
    private UriSigner $uriSigner;
    private RouterInterface $router;
    private ScopeMatcher $scopeMatcher;

    public function __construct(RequestStack $requestStack, Environment $twig, TranslatorInterface $translator, UriSigner $uriSigner, RouterInterface $router, ScopeMatcher $scopeMatcher)
    {
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->uriSigner = $uriSigner;
        $this->router = $router;
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(Template $objTemplate): void
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        // Skip listener if wu have a cron request
        if (null === $request) {
            return;
        }

        $session = $request->getSession();

        /*
         * Do only show the password forgotten button
         * if the user entered the right username but a wroong password.
         */
        $blnInvalidUsername = false;

        if ($session->getFlashBag()->has('invalidUsername')) {
            $blnInvalidUsername = true;
            $session->getFlashBag()->get('invalidUsername');
        }

        if (!$blnInvalidUsername && $this->scopeMatcher->isBackendRequest($request)) {
            if (0 === strpos($objTemplate->getName(), 'be_login')) {
                // Generate password recover link
                $locale = $request->getLocale();

                $href = sprintf(
                    $this->router->generate(
                        'backend_password_recovery_requirepasswordrecoverylink_form',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ).'?_locale=%s',
                    $locale
                );

                $signedUri = $this->uriSigner->sign($href);
                $objTemplate->recoverPasswordLink = $signedUri;

                // Forgot password label
                $objTemplate->forgotPassword = $this->translator->trans('MSC.forgotPassword', [], 'contao_default');

                // Show reset password link if login has failed
                if (false !== strpos($objTemplate->messages, substr($this->translator->trans('ERR.invalidLogin', [], 'contao_default'), 0, 10)) || false !== strpos($objTemplate->messages, substr($this->translator->trans('ERR.accountLocked', [], 'contao_default'), 0, 10))) {
                    $objTemplate->messages .= $this->twig->render(
                        '@MarkocupicBackendPasswordRecovery/password_recovery_button.html.twig',
                        [
                            'href' => $signedUri,
                            'recoverPassword' => $this->translator->trans('MSC.recoverPassword', [], 'contao_default'),
                        ]
                    );
                }
            }
        }
    }
}
