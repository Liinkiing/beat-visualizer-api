<?php


namespace App\EventListener;


use App\Client\SpotifyClient;
use App\Model\Spotify\User;
use Overblog\GraphQLBundle\Event\ExecutorArgumentsEvent;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SpotifyUserInitialization
{
    public const UNAUTHORIZED_REQUEST_MESSAGE = 'UNAUTHORIZED';

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
            try {
                $userFromApi = $this->client->me($accessToken);
                $event->setRootValue(
                    User::createFromApi($userFromApi)
                );
            } catch (ClientException $exception) {
                if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                    $event->setRootValue(null);
                    // Here, we catch the unauthorize response to handle auto refresh of access token if user have a refresh token
                    // So we return an UnauthorizedHttpException to handle this in the frontend and use Apollo Link to auto retry this request
                    throw new UnauthorizedHttpException('invalid_token');
                }
            } catch (\Exception $exception) {
                $event->setRootValue(null);
            }
        } else {
            $event->setRootValue(null);
        }
    }
}
