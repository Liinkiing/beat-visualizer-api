<?php


namespace App\GraphQL\Resolver\Type;


use App\Model\PaginatedItem;
use App\Model\Spotify\UserPlaylist;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

class PaginatedItemTypeResolver implements ResolverInterface
{
    private $resolver;

    public function __construct(TypeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(PaginatedItem $item): Type
    {
        if ($item instanceof UserPlaylist) {
            return $this->resolver->resolve('SpotifyUserPlaylist');
        }

        throw new UserError('Could not resolve type of the paginated list item.');
    }
}
