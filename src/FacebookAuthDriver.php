<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Auth\Facebook;

use Exception;
use Flarum\Forum\Auth\SsoDriverInterface;
use Flarum\Forum\Auth\SsoResponse;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Translation\TranslatorInterface;

class FacebookAuthDriver implements SsoDriverInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param SettingsRepositoryInterface $settings
     * @param TranslatorInterface $translator
     * @param UrlGenerator $url
     */
    public function __construct(SettingsRepositoryInterface $settings, TranslatorInterface $translator, UrlGenerator $url)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->url = $url;
    }

    public function meta(): array
    {
        return [
            "name" => "Facebook",
            "icon" => "fab fa-facebook",
            "buttonColor" => "#3b5998",
            "buttonText" => $this->translator->trans('flarum-auth-facebook.forum.log_in.with_facebook_button'),
            "buttonTextColor" => "#fff",
        ];
    }

    /**
     * @param Request $request
     * @param SsoResponse $ssoResponse
     * @throws \League\OAuth2\Client\Provider\Exception\FacebookProviderException
     * @throws Exception
     */
    public function sso(Request $request, SsoResponse $ssoResponse)
    {
        $redirectUri = $this->url->to('forum')->route('sso', ['provider' => 'facebook']);

        $provider = new Facebook([
            'clientId' => $this->settings->get('flarum-auth-facebook.app_id'),
            'clientSecret' => $this->settings->get('flarum-auth-facebook.app_secret'),
            'redirectUri' => $redirectUri,
            'graphApiVersion' => 'v3.0',
        ]);

        $session = $request->getAttribute('session');
        $queryParams = $request->getQueryParams();

        $code = array_get($queryParams, 'code');

        if (! $code) {
            $authUrl = $provider->getAuthorizationUrl();
            $session->put('oauth2state', $provider->getState());

            return new RedirectResponse($authUrl.'&display=popup');
        }

        $state = array_get($queryParams, 'state');

        if (! $state || $state !== $session->get('oauth2state')) {
            $session->remove('oauth2state');

            throw new Exception('Invalid state');
        }

        $token = $provider->getAccessToken('authorization_code', compact('code'));

        /** @var FacebookUser $user */
        $user = $provider->getResourceOwner($token);

        return $ssoResponse
            ->withIdentifier($user->getId())
            ->provideTrustedEmail($user->getEmail())
            ->provideAvatar($user->getPictureUrl())
            ->suggestUsername($user->getName())
            ->setPayload($user->toArray());
    }
}
