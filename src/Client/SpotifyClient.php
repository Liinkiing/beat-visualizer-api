<?php

namespace App\Client;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpotifyClient
{
    private const SCOPES = [
        'user-read-playback-state',
        'user-read-currently-playing',
        'streaming',
        'user-read-birthdate',
        'user-modify-playback-state',
        'user-read-private',
        'playlist-read-private',
        'playlist-modify-private',
        'user-read-email',
        'user-library-read',
        'user-library-modify',
    ];
    private const API_BASE_URL = 'https://api.spotify.com/v1';
    private const ACCOUNTS_BASE_URL = 'https://accounts.spotify.com/';
    private const OAUTH_2_GRANT_TYPE_AUTHORIZATION = 'authorization_code';
    private const OAUTH_2_GRANT_TYPE_REFRESH = 'refresh_token';

    protected $redirectUri;
    protected $clientId;
    protected $clientSecret;
    protected $http;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri, HttpClientInterface $http)
    {
        $this->redirectUri = $redirectUri;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->http = $http;
    }

    public function getAuthorizeUri(): Response
    {
        $options = [
            'query' => [
                'client_id' => $this->clientId,
                'response_type' => 'code',
                'scope' => $this->getScopes(),
                'redirect_uri' => $this->redirectUri
            ]
        ];

        $request = $this->http->request('GET', self::ACCOUNTS_BASE_URL . 'authorize', $options);

        return new RedirectResponse(
            $request->getInfo('url')
        );
    }

    public function askToken(string $authorizationCode): Response
    {
        $options = [
            'body' => [
                'grant_type' => self::OAUTH_2_GRANT_TYPE_AUTHORIZATION,
                'code' => $authorizationCode,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ];

        $response = $this->http->request('POST', self::ACCOUNTS_BASE_URL . 'api/token', $options);

        return new JsonResponse(
            $response->toArray(false),
            $response->getStatusCode()
        );
    }

    public function askNewToken(string $refreshToken): Response
    {
        $options = [
            'body' => [
                'grant_type' => self::OAUTH_2_GRANT_TYPE_REFRESH,
                'refresh_token' => $refreshToken,
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode(
                        $this->clientId . ':' . $this->clientSecret
                    )
            ]
        ];

        $response = $this->http->request('POST', self::ACCOUNTS_BASE_URL . 'api/token', $options);

        return new JsonResponse(
            $response->toArray(false),
            $response->getStatusCode()
        );
    }

    private function getScopes(): string
    {
        return implode(' ', self::SCOPES);
    }
}
