<?php

namespace App\GraphQL\Mutation\Player;

use App\Client\SpotifyClient;
use App\GraphQL\Resolver\SpotifyUser\SpotifyUserPlayerResolver;
use App\Model\Spotify\User;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class ChangeUserPlayerStateMutation implements MutationInterface
{

    private $client;
    private $playerResolver;

    public function __construct(SpotifyClient $client, SpotifyUserPlayerResolver $playerResolver)
    {
        $this->client = $client;
        $this->playerResolver = $playerResolver;
    }

    public function __invoke(User $viewer, Argument $args)
    {
        [$repeat, $shuffle] = [$args->offsetGet('repeat'), $args->offsetGet('shuffle')];

        $this->client->changeMePlayerState($viewer->getAccessToken(), $repeat, $shuffle);

        return ['player' => $this->playerResolver->__invoke($viewer)];
    }

}
