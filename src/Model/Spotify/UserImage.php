<?php


namespace App\Model\Spotify;


use App\Model\ApiModel;

class UserImage implements ApiModel
{

    protected $height;
    protected $width;
    protected $url;

    public static function createFromApi(array $apiResponse): self
    {
        return new self(
            $apiResponse['url'],
            $apiResponse['height'],
            $apiResponse['width']
        );
    }

    private function __construct(
        string $url,
        ?int $height,
        ?int $width
    )
    {
        $this->height = $height;
        $this->width = $width;
        $this->url = $url;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

}
