<?php

namespace App\GraphQL\Resolver;

use App\Model\Spotify\User;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class UserIsLoggedThroughSpotifyResolver implements ResolverInterface
{

    public function resolve($resolveInfo): bool
    {
        return $resolveInfo->rootValue instanceof User;
    }

}
