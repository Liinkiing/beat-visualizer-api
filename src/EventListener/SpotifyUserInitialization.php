<?php


namespace App\EventListener;


use App\Client\SpotifyClient;
use App\Model\Spotify\User;
use Overblog\GraphQLBundle\Event\ExecutorArgumentsEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class SpotifyUserInitialization
{
    protected $client;
    private $stack;

    public function __construct(SpotifyClient $client, RequestStack $stack)
    {
        $this->client = $client;
        $this->stack = $stack;
    }

    public function onPreExecutor(ExecutorArgumentsEvent $event): void
    {
        $accessToken = str_replace(
            'Bearer ',
            '',
            trim($this->stack->getCurrentRequest()->headers->get('Authorization'))
        );
        if ($accessToken) {
            $event->setRootValue($accessToken);
            try {
                $userFromApi = $this->client->me($accessToken);
                $event->setRootValue(
                    User::createFromApi($userFromApi)
                );
            } catch (\Exception $exception) {
                $event->setRootValue(null);
            }
        } else {
            $event->setRootValue(null);
        }
    }
}
