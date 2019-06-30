<?php

namespace App\GraphQL\Resolver\SpotifyUser;

use App\Client\SpotifyClient;
use App\Model\Spotify\User;
use App\Model\Spotify\UserPlayer;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class SpotifyUserPlayerResolver implements ResolverInterface
{

    private $client;

    public function __construct(SpotifyClient $client)
     {
         $this->client = $client;
     }

    public function __invoke(User $viewer): UserPlayer
    {
        return UserPlayer::createFromApi(
            $this->client->mePlayer(
                $viewer->getAccessToken()
            )
        );
    }

}
