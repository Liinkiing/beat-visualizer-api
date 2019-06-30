<?php

namespace App\GraphQL\Resolver\SpotifyUser;

use App\Client\SpotifyClient;
use App\Model\Spotify\PaginatedList;
use App\Model\Spotify\User;
use App\Model\Spotify\UserPlaylist;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class SpotifyUserPlaylistsResolver implements ResolverInterface
{

    private $client;

    public function __construct(SpotifyClient $client)
    {
        $this->client = $client;
    }

    public function __invoke(User $viewer, Argument $args): PaginatedList
    {
        [$limit, $offset] = [$args->offsetGet('limit'), $args->offsetGet('offset')];

        $response = $this->client->mePlaylists(
            $viewer->getAccessToken(),
            $limit,
            $offset
        );

        return PaginatedList::createPaginatedFromApi(
            $response,
            UserPlaylist::class
        );
    }

}
