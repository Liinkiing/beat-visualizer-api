<?php

namespace App\GraphQL\Resolver\Query;

use App\Model\Spotify\User;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class ViewerResolver implements ResolverInterface
{

    public function __invoke($resolveInfo): ?User
    {
        if ($resolveInfo->rootValue instanceof User) {
            return $resolveInfo->rootValue;
        }

        return null;
    }

}
