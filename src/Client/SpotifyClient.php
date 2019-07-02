<?php

namespace App\Client;

use App\Enum\SpotifyUserRepeatMode;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

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
    private const API_BASE_URL = 'https://api.spotify.com/v1/';
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

    public function askToken(string $authorizationCode): array
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

        return $response->toArray();
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

    public function me(string $accessToken): array
    {
        $response = $this->makeAuthRequest($accessToken, 'me');

        return $response->toArray();
    }

    public function mePlayer(string $accessToken): array
    {
        $response = $this->makeAuthRequest($accessToken, 'me/player');

        return $response->toArray();
    }

    public function mePlaylists(string $accessToken, int $limit = 20, $offset = 0): array
    {
        $response = $this->makeAuthRequest($accessToken, 'me/playlists', compact('limit', 'offset'));

        return $response->toArray();
    }

    public function changeMePlayerState(string $accessToken, string $repeat, bool $shuffle): array
    {
        // I use the Symfony HTTP Client concurrent requests feature here to speed up time
        $responses = [
            $this->changeMePlayerRepeat($accessToken, $repeat),
            $this->changeMePlayerShuffle($accessToken, $shuffle),
        ];

        try {
            $contents = array_map(static function (ResponseInterface $response) {
                return $response->getContent();
            }, $responses);
            return ['success' => true];
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function changeMePlayerShuffle(string $accessToken, bool $state, ?string $deviceId = null): ResponseInterface
    {
        $query = [
            'state' => $state ? 'true' : 'false',
            'deviceId' => $deviceId
        ];

        return $this->makeAuthRequest($accessToken, 'me/player/shuffle', $query, 'PUT', ['Content-Length' => '0']);
    }

    public function toggleMePlayerPlay(string $accessToken, ?string $deviceId = null): void
    {
        $query = [
            'deviceId' => $deviceId
        ];
        $headers = ['Content-Length' => '0'];
        $method = 'PUT';
        $path = 'me/player/pause';

        try {
            $this->makeAuthRequest($accessToken, $path, $query, $method, $headers)->getContent();
        } catch (ClientException $exception) {
            // If a forbidden is thrown here, it's mean that, for the spotify API, the 'playing' boolean should be
            // the opposite, to allow a correct toggle behaviour
            if ($exception->getCode() === Response::HTTP_FORBIDDEN) {
                $path = 'me/player/play';
                try {
                    $this->makeAuthRequest($accessToken, $path, $query, $method, $headers)->getContent();
                } catch (\Exception $exception) {
                }
            }
        }
    }

    private function changeMePlayerRepeat(string $accessToken, string $state, ?string $deviceId = null): ResponseInterface
    {
        if (!SpotifyUserRepeatMode::isValueValid($state)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid repeat mode "%s". Available modes: %s',
                    $state,
                    implode(',', SpotifyUserRepeatMode::getAvailableValues())
                )
            );
        }

        $query = compact('state', 'deviceId');

        return $this->makeAuthRequest($accessToken, 'me/player/repeat', $query, 'PUT', ['Content-Length' => '0']);
    }

    private function makeAuthRequest(string $accessToken, string $path, array $query = [], ?string $method = 'GET', ?array $headers = []): ResponseInterface
    {
        $options = [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $accessToken
            ], $headers)
        ];
        $options = array_merge(
            $options,
            compact('query')
        );

        return $this->http->request($method, $this->endpoint($path), $options);
    }

    private function endpoint(string $path): string
    {
        return self::API_BASE_URL . $path;
    }

    private function getScopes(): string
    {
        return implode(' ', self::SCOPES);
    }
}
