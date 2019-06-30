<?php

namespace App\GraphQL\Resolver\SpotifyUser;

use App\Enum\ProgressionUnit;
use App\Model\Spotify\UserPlayer;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use function Utils\msToMinutesSeconds;

class SpotifyUserPlayerProgressionResolver implements ResolverInterface
{

    public function __invoke(UserPlayer $player, Argument $args)
    {
        $format = $args->offsetGet('format');
        if ($format && $format === ProgressionUnit::MINUTES_SECONDS) {
            return msToMinutesSeconds($player->getProgression());
        }
        return $player->getProgression();
    }

}
