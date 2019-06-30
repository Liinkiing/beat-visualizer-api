<?php


namespace App\Controller;

use App\Client\SpotifyClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/spotify")
 */
class SpotifyController extends AbstractController
{

    private $client;
    private $clientRedirectUrl;

    protected const REFRESH_TOKEN_PARAM_NAME = 'refresh_token';
    protected const AUTH_CODE_PARAM_NAME = 'code';

    public function __construct(SpotifyClient $client, string $clientRedirectUrl)
    {
        $this->client = $client;
        $this->clientRedirectUrl = $clientRedirectUrl;
    }

    /**
     * @Route("/authorize", name="spotify.authorize", methods={"GET"})
     */
    public function authorizeAction(): Response
    {
        return $this->client->getAuthorizeUri();
    }

    /**
     * @Route("/authorize/callback",
     *     name="spotify.authorize.callback",
     *     methods={"GET"},
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"}),
     */
    public function authCallbackAction(Request $request): RedirectResponse
    {
        $code = $request->query->get(self::AUTH_CODE_PARAM_NAME);

        ['access_token' => $access_token, 'refresh_token' => $refresh_token] = $this->client->askToken($code);

        $query = http_build_query(compact('access_token', 'refresh_token'));

        return $this->redirect(
            "$this->clientRedirectUrl?$query"
        );
    }

    /**
     * @Route("/token/refresh",
     *     name="spotify.token.refresh",
     *     methods={"POST"},
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"}),
     */
    public function refreshTokenAction(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if (!$body) {
            return $this->json(
                ['message' => 'Bad Request', 'status' => Response::HTTP_BAD_REQUEST],
                Response::HTTP_BAD_REQUEST
            );
        }
        $refreshToken = $body[self::REFRESH_TOKEN_PARAM_NAME];

        return $this->client->askNewToken($refreshToken);
    }

}
