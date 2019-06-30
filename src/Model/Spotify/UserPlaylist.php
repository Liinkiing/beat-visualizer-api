<?php


namespace App\Model\Spotify;


use App\Model\ApiModel;
use App\Model\PaginatedItem;
use App\Traits\PaginatedItemTrait;

class UserPlaylist implements ApiModel, PaginatedItem
{

    use PaginatedItemTrait;

    protected $uri;
    protected $href;
    protected $images;
    protected $name;
    protected $public;

    public static function createFromApi(array $apiResponse): self
    {
        return new self(
            $apiResponse['id'],
            $apiResponse['uri'],
            $apiResponse['href'],
            array_map([UserImage::class, 'createFromApi'], $apiResponse['images'] ?? []),
            $apiResponse['name'],
            $apiResponse['public']
        );
    }

    private function __construct(
        string $id,
        string $uri,
        string $href,
        array $images,
        string $name,
        ?bool $public
    )
    {
        $this->id = $id;
        $this->uri = $uri;
        $this->href = $href;
        $this->images = $images;
        $this->name = $name;
        $this->public = $public;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return UserImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }


}
