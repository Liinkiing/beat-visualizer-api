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

    public function __construct(SpotifyClient $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/authorize", name="spotify.authorize", methods={"GET"})
     */
    public function authorizeAction(): Response
    {
        return $this->client->authorize();
    }

    /**
     * @Route("/auth-callback", name="spotify.auth-callback", methods={"GET"})
     */
    public function authCallbackAction(Request $request): Response
    {
        $code = $request->query->get('code');

        return $this->client->askToken($code);
    }

}
