<?php


namespace App\Controller;

use App\Client\SpotifyClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/spotify")
 */
class SpotifyController extends AbstractController
{

    private $client;

    protected const REFRESH_TOKEN_PARAM_NAME = 'refresh_token';
    protected const AUTH_CODE_PARAM_NAME = 'code';

    public function __construct(SpotifyClient $client)
    {
        $this->client = $client;
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
    public function authCallbackAction(Request $request): Response
    {
        $code = $request->query->get(self::AUTH_CODE_PARAM_NAME);

        return $this->client->askToken($code);
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
