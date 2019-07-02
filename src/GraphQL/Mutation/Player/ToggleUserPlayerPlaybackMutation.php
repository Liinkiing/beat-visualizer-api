<?php

namespace App\GraphQL\Mutation\Player;

use App\Client\SpotifyClient;
use App\GraphQL\Resolver\SpotifyUser\SpotifyUserPlayerResolver;
use App\Model\Spotify\User;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class ToggleUserPlayerPlaybackMutation implements MutationInterface
{

    private $client;
    private $playerResolver;

    public function __construct(SpotifyClient $client, SpotifyUserPlayerResolver $playerResolver)
     {
         $this->client = $client;
         $this->playerResolver = $playerResolver;
     }

    public function __invoke(User $viewer)
    {
        $this->client->toggleMePlayerPlay(
            $viewer->getAccessToken()
        );

        return ['player' => $this->playerResolver->__invoke($viewer)];

    }

}
